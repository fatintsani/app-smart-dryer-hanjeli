import base64
from contextlib import contextmanager, redirect_stdout
import hashlib
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import importlib.util
import io
import json
from pathlib import Path
import re
import threading
import unittest
from unittest.mock import patch

spec = importlib.util.spec_from_file_location('verify_frontend', Path(__file__).resolve().parents[2] / 'scripts/verify_frontend.py')
verify = importlib.util.module_from_spec(spec)
spec.loader.exec_module(verify)

JS = '/build/assets/app-NEW12345.js'
CSS = '/build/assets/app-NEW12345.css'
HTML = f'<html><script type="module" src="{JS}"></script><link rel="stylesheet" href="{CSS}"><link rel="icon" href="/icon.png"></html>'.encode()


@contextmanager
def server(routes):
    class Handler(BaseHTTPRequestHandler):
        def do_GET(self):
            status, content_type, body = routes.get(self.path, (404, 'text/plain', b'missing'))
            self.send_response(status)
            self.send_header('Content-Type', content_type)
            if status == 302:
                self.send_header('Location', JS)
            self.end_headers()
            self.wfile.write(body)

        def log_message(self, *args):
            pass

    http = ThreadingHTTPServer(('127.0.0.1', 0), Handler)
    worker = threading.Thread(target=http.serve_forever, daemon=True)
    worker.start()
    try:
        yield f'http://127.0.0.1:{http.server_port}'
    finally:
        http.shutdown()
        http.server_close()
        worker.join()


def routes():
    return {
        '/': (200, 'text/html', HTML), '/up': (200, 'text/html', b'healthy'),
        JS: (200, 'application/javascript', b'console.log("new deployment")'),
        CSS: (200, 'text/css', b'body { color: green }'),
        '/icon.png': (200, 'image/png', b'image'),
        '/build/version.json': (200, 'application/json', b'{"revision":"tested-sha"}'),
        '/build/deployment.json': (200, 'application/json', b'{"VITE_REVERB_APP_KEY":"public-test-key"}'),
    }


class FrontendVerificationTests(unittest.TestCase):
    def check(self, public, docker=None, **options):
        with server(docker or routes()) as docker_origin, server(public) as public_origin, redirect_stdout(io.StringIO()):
            verify.verify_frontend(docker_origin, public_origin, **options)

    def test_matching_bundles_and_assets_pass(self):
        self.check(routes(), expected_revision='tested-sha')

    def test_healthy_up_does_not_hide_legacy_javascript(self):
        public = routes()
        public['/'] = (200, 'text/html', HTML.replace(b'NEW12345.js', b'OLD12345.js'))
        public['/build/assets/app-OLD12345.js'] = (200, 'application/javascript', b'old JS')
        with self.assertRaisesRegex(verify.VerificationError, '127.0.0.1:8088'):
            self.check(public)

    def test_missing_hashed_javascript_fails(self):
        public = routes()
        public['/'] = (200, 'text/html', b'<html>healthy old application</html>')
        with self.assertRaisesRegex(verify.VerificationError, 'no hashed Vite JavaScript'):
            self.check(public)

    def test_same_filename_but_different_javascript_bytes_fails(self):
        public = routes()
        public[JS] = (200, 'application/javascript', b'wrong cached bytes')
        with self.assertRaisesRegex(verify.VerificationError, 'asset bytes differ'):
            self.check(public)

    def test_missing_public_stylesheet_fails(self):
        public = routes()
        del public[CSS]
        with self.assertRaisesRegex(verify.VerificationError, 'expected HTTP 200, got 404'):
            self.check(public)

    def test_html_fallback_at_asset_url_fails_even_with_status_200(self):
        public = routes()
        public[CSS] = (200, 'text/html', b'<html>fallback</html>')
        with self.assertRaisesRegex(verify.VerificationError, 'returned HTML'):
            self.check(public)

    def test_asset_redirect_is_not_accepted_as_http_200(self):
        public = routes()
        public[CSS] = (302, 'text/plain', b'redirect')
        with self.assertRaisesRegex(verify.VerificationError, 'expected HTTP 200, got 302'):
            self.check(public)

    def test_wrong_public_revision_fails(self):
        public = routes()
        public['/build/version.json'] = (200, 'application/json', b'{"revision":"old-sha"}')
        with self.assertRaisesRegex(verify.VerificationError, 'revision does not match'):
            self.check(public, expected_revision='tested-sha')

    def test_absolute_public_asset_urls_in_docker_html_are_compared(self):
        docker_origin, public_origin = 'http://127.0.0.1:8088', 'https://hanjeli-dryer.stas-rg.com'
        parser = verify.AssetParser(docker_origin, {docker_origin, public_origin})
        parser.feed(f'<script src="{public_origin}{JS}"></script><script src="https://external.test/other.js"></script>')
        self.assertEqual(parser.javascript, {JS})
        self.assertEqual(parser.assets, {JS})

    def test_wss_key_comes_from_docker_public_build_metadata(self):
        with patch.object(verify, 'verify_wss') as probe:
            self.check(routes(), wss=True)
            self.assertEqual(probe.call_args.args[1], 'public-test-key')


class WssVerificationTests(unittest.TestCase):
    class Socket:
        def __init__(self, status=101, valid_accept=True):
            self.status, self.valid_accept = status, valid_accept
            self.sent = b''

        def __enter__(self):
            return self

        def __exit__(self, *args):
            return False

        def sendall(self, request):
            self.sent = request

        def recv(self, size):
            nonce = re.search(rb'Sec-WebSocket-Key: ([^\r]+)', self.sent).group(1)
            accept = base64.b64encode(hashlib.sha1(nonce + b'258EAFA5-E914-47DA-95CA-C5AB0DC85B11').digest()).decode()
            if not self.valid_accept:
                accept = 'invalid'
            return f'HTTP/1.1 {self.status} Result\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: {accept}\r\n\r\n'.encode()

    def probe(self, connection):
        with patch.object(verify.socket, 'create_connection', return_value=connection), patch.object(verify.ssl, 'create_default_context') as context, redirect_stdout(io.StringIO()):
            context.return_value.wrap_socket.return_value = connection
            verify.verify_wss('https://hanjeli-dryer.stas-rg.com', 'public-key')
            context.return_value.wrap_socket.assert_called_once_with(connection, server_hostname='hanjeli-dryer.stas-rg.com')

    def test_valid_tls_upgrade_passes_without_subscribing_or_publishing(self):
        connection = self.Socket()
        self.probe(connection)
        self.assertIn(b'GET /app/public-key?', connection.sent)
        self.assertNotIn(b'pusher:subscribe', connection.sent)

    def test_non_101_upgrade_fails(self):
        with self.assertRaisesRegex(verify.VerificationError, 'upgrade failed'):
            self.probe(self.Socket(status=502))

    def test_invalid_accept_header_fails(self):
        with self.assertRaisesRegex(verify.VerificationError, 'invalid WebSocket handshake'):
            self.probe(self.Socket(valid_accept=False))


if __name__ == '__main__':
    unittest.main()
