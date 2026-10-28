#!/usr/bin/env python3
"""Read-only deployment checks: compare the Docker frontend with public HTTPS."""

import argparse
import base64
import hashlib
from html.parser import HTMLParser
import json
import os
import re
import socket
import ssl
import sys
from urllib.error import HTTPError, URLError
from urllib.parse import quote, urljoin, urlsplit
from urllib.request import HTTPRedirectHandler, Request, build_opener


class VerificationError(RuntimeError):
    pass


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


def fetch_url(url, headers=None):
    request = Request(url, headers={
        'User-Agent': 'Hanjeli-Deployment-Verification/1.0',
        'Cache-Control': 'no-cache', 'Pragma': 'no-cache', 'Accept-Encoding': 'identity',
        **(headers or {}),
    })
    try:
        with build_opener(NoRedirect).open(request, timeout=20) as response:
            if response.status != 200:
                raise VerificationError(f'{url}: expected HTTP 200, got {response.status}')
            return response.read(), response.headers.get_content_type()
    except HTTPError as error:
        raise VerificationError(f'{url}: expected HTTP 200, got {error.code}') from None
    except (URLError, TimeoutError, OSError) as error:
        raise VerificationError(f'{url}: request failed ({error})') from None


class AssetParser(HTMLParser):
    def __init__(self, origin, accepted_origins):
        super().__init__()
        self.origin = origin
        self.accepted_origins = accepted_origins
        self.assets = set()
        self.javascript = set()

    def handle_starttag(self, tag, attributes):
        attrs = dict(attributes)
        reference = None
        if tag in {'script', 'img', 'source'}:
            reference = attrs.get('src')
        elif tag == 'link' and set(attrs.get('rel', '').lower().split()) & {
            'stylesheet', 'modulepreload', 'icon', 'apple-touch-icon', 'manifest',
        }:
            reference = attrs.get('href')
        if not reference:
            return
        url = urlsplit(urljoin(self.origin + '/', reference))
        if f'{url.scheme}://{url.netloc}' not in self.accepted_origins:
            return  # Third-party fonts/identity scripts are outside this deployment.
        path = url.path + (f'?{url.query}' if url.query else '')
        self.assets.add(path)
        if re.fullmatch(r'/build/assets/[^/]+-[A-Za-z0-9_-]{6,}\.js', url.path):
            self.javascript.add(url.path)


def verify_wss(public_origin, app_key):
    """Validate TLS + RFC 6455 upgrade; do not subscribe or publish telemetry."""
    url = urlsplit(public_origin)
    if url.scheme != 'https' or not url.hostname:
        raise VerificationError('Public WSS verification requires an HTTPS origin')
    nonce = base64.b64encode(os.urandom(16)).decode('ascii')
    path = f'/app/{quote(app_key, safe="")}?protocol=7&client=js&version=8.6.0&flash=false'
    request = (
        f'GET {path} HTTP/1.1\r\nHost: {url.netloc}\r\nOrigin: {public_origin}\r\n'
        'Upgrade: websocket\r\nConnection: Upgrade\r\n'
        f'Sec-WebSocket-Key: {nonce}\r\nSec-WebSocket-Version: 13\r\n\r\n'
    )
    try:
        with socket.create_connection((url.hostname, url.port or 443), timeout=15) as connection:
            context = ssl.create_default_context()
            with context.wrap_socket(connection, server_hostname=url.hostname) as secure:
                secure.sendall(request.encode('ascii'))
                response = b''
                while b'\r\n\r\n' not in response:
                    chunk = secure.recv(4096)
                    if not chunk or len(response) + len(chunk) > 65536:
                        raise VerificationError('Public WSS returned incomplete/oversized upgrade headers')
                    response += chunk
                lines = response.split(b'\r\n\r\n', 1)[0].decode('iso-8859-1').split('\r\n')
                if not re.match(r'^HTTP/1\.[01] 101(?: |$)', lines[0]):
                    raise VerificationError(f'Public WSS upgrade failed: {lines[0]}')
                headers = {name.lower().strip(): value.strip() for name, value in (
                    line.split(':', 1) for line in lines[1:] if ':' in line
                )}
                accept = base64.b64encode(hashlib.sha1((nonce + '258EAFA5-E914-47DA-95CA-C5AB0DC85B11').encode()).digest()).decode()
                connection_tokens = {token.strip().lower() for token in headers.get('connection', '').split(',')}
                if headers.get('sec-websocket-accept') != accept or headers.get('upgrade', '').lower() != 'websocket' or 'upgrade' not in connection_tokens:
                    raise VerificationError('Public WSS returned invalid WebSocket handshake headers')
    except (OSError, TimeoutError) as error:
        raise VerificationError(f'Public WSS TLS/connection failed: {error}') from None
    print('PASS: public WSS TLS and WebSocket upgrade HTTP 101')


def verify_frontend(docker_origin, public_origin, expected_revision=None, wss=False):
    accepted_origins = {docker_origin, public_origin}
    # Generate absolute Laravel asset URLs as the HTTPS edge would, while still
    # connecting directly to the Docker published port rather than host Nginx.
    edge_headers = {'Host': urlsplit(public_origin).netloc, 'X-Forwarded-Proto': 'https'}
    parsers = []
    for name, origin, headers in [('Docker', docker_origin, edge_headers), ('public', public_origin, {})]:
        body, content_type = fetch_url(origin + '/', headers)
        if content_type != 'text/html':
            raise VerificationError(f'{name} root must return HTML, got {content_type}')
        parser = AssetParser(origin, accepted_origins)
        parser.feed(body.decode('utf-8'))
        if not parser.javascript:
            raise VerificationError(f'{name} HTML contains no hashed Vite JavaScript under /build/assets/')
        parsers.append(parser)

    docker, public = parsers
    print('Docker Vite JavaScript:', ', '.join(sorted(docker.javascript)))
    print('Public Vite JavaScript:', ', '.join(sorted(public.javascript)))
    if docker.javascript != public.javascript:
        raise VerificationError(
            'Public frontend differs from the new Docker frontend. Check host Nginx: '
            'the domain, /build/, /assets/, /app and /apps must proxy to 127.0.0.1:8088, '
            'not the legacy port 8080. Also inspect edge/HTML caches.'
        )

    for path in sorted(public.assets):
        public_body, public_type = fetch_url(public_origin + path)
        docker_body, docker_type = fetch_url(docker_origin + path, edge_headers)
        if public_type == 'text/html' or docker_type == 'text/html':
            raise VerificationError(f'{path}: asset returned HTML instead of a frontend asset')
        if urlsplit(path).path.endswith('.js') and public_type not in {'text/javascript', 'application/javascript', 'application/x-javascript'}:
            raise VerificationError(f'{path}: JavaScript asset has invalid content type {public_type}')
        if hashlib.sha256(public_body).digest() != hashlib.sha256(docker_body).digest():
            raise VerificationError(f'{path}: asset bytes differ between Docker and public domain; inspect Nginx upstream/static locations and caches')
        print(f'PASS: HTTP 200 and identical asset bytes {path}')

    if expected_revision:
        for name, origin, headers in [('Docker', docker_origin, edge_headers), ('public', public_origin, {})]:
            body, _ = fetch_url(origin + '/build/version.json', headers)
            try:
                version = json.loads(body)
            except (ValueError, UnicodeError):
                raise VerificationError(f'{name} frontend version metadata is invalid') from None
            if not isinstance(version, dict) or version.get('revision') != expected_revision:
                raise VerificationError(f'{name} frontend revision does not match the tested deployment SHA')
        print('PASS: Docker and public frontend revisions match the tested SHA')

    if wss:
        body, _ = fetch_url(docker_origin + '/build/deployment.json', edge_headers)
        try:
            metadata = json.loads(body)
            app_key = metadata.get('VITE_REVERB_APP_KEY') if isinstance(metadata, dict) else None
        except (ValueError, UnicodeError):
            raise VerificationError('Docker public Reverb build metadata is invalid') from None
        if not isinstance(app_key, str) or not app_key:
            raise VerificationError('Docker frontend has no public Reverb app key for WSS verification')
        verify_wss(public_origin, app_key)
    print('PASS: public frontend matches the new Docker deployment')


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--docker-origin', default='http://127.0.0.1:8088')
    parser.add_argument('--public-origin', default='https://hanjeli-dryer.stas-rg.com')
    parser.add_argument('--expected-revision')
    parser.add_argument('--wss', action='store_true', help='Require a verified public TLS WebSocket upgrade')
    args = parser.parse_args()
    for name, origin in [('Docker', args.docker_origin), ('public', args.public_origin)]:
        url = urlsplit(origin)
        if not url.hostname or url.username or url.password or url.path not in {'', '/'} or url.query or url.fragment:
            parser.error(f'{name} origin must be a plain origin without credentials, path, query or fragment')
        if url.scheme not in ({'http', 'https'} if name == 'Docker' else {'https'}):
            parser.error(f'{name} origin must use {"HTTP(S)" if name == "Docker" else "HTTPS"}')
    try:
        verify_frontend(args.docker_origin.rstrip('/'), args.public_origin.rstrip('/'), args.expected_revision, args.wss)
    except (VerificationError, UnicodeError, ValueError) as error:
        print(f'FAIL: {error}', file=sys.stderr)
        return 1
    return 0


if __name__ == '__main__':
    sys.exit(main())
