<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function verify(bool $condition, string $name): void
{
    if (! $condition) {
        fwrite(STDERR, "FAIL: $name\n");
        exit(1);
    }
    echo "PASS: $name\n";
}

function request(string $path): array
{
    $headers = [];
    $curl = curl_init('http://web'.$path);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Host: hanjeli-dryer.stas-rg.com', 'X-Forwarded-Proto: https'],
        CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$headers): int {
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
            return strlen($line);
        },
    ]);
    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);
    return [$status, $body, $headers];
}

verify(Illuminate\Support\Facades\DB::select('SELECT 1') !== [], 'MySQL connection');
verify(config('app.timezone') === 'Asia/Jakarta', 'Laravel timezone Asia/Jakarta');
[$status, $body] = request('/up');
verify($status === 200, 'Laravel /up via Nginx/FPM');
[$status, $body, $headers] = request('/');
verify($status === 200 && str_contains($body, '/build/assets/'), 'Vue HTML and Vite references');
verify(str_contains($headers['cache-control'] ?? '', 'no-store'), 'Application HTML is not cached');
[$status] = request('/monitoring');
verify($status === 200, 'Vue history fallback');
[$status, $body] = request('/api/batches');
verify($status === 200 && json_decode($body, true) !== null, 'Laravel API with MySQL');
$manifest = json_decode(file_get_contents(__DIR__.'/../public/build/manifest.json'), true, flags: JSON_THROW_ON_ERROR);
foreach ($manifest as $asset) {
    [$status, , $headers] = request('/build/'.$asset['file']);
    verify($status === 200, 'Vite asset '.$asset['file']);
    if (str_starts_with($asset['file'], 'assets/')) {
        verify(str_contains($headers['cache-control'] ?? '', 'immutable'), 'Hashed Vite assets are immutable');
    }
}
foreach (['/assets/icons/profile/cs.png', '/assets/img/ai_profile.jpg', '/assets/icons/hero/hero.png'] as $path) {
    [$status, $body, $headers] = request($path);
    verify($status === 200 && str_starts_with($headers['content-type'] ?? '', 'image/'), 'Image HTTP 200 '.$path);
}
foreach (['/pwa-192x192.png' => 192, '/pwa-512x512.png' => 512, '/apple-touch-icon.png' => 180] as $path => $size) {
    [$status, $body] = request($path);
    $dimensions = strlen($body) >= 24 ? unpack('Nwidth/Nheight', substr($body, 16, 8)) : [];
    verify($status === 200 && ($dimensions['width'] ?? 0) === $size && ($dimensions['height'] ?? 0) === $size, 'PWA icon dimensions '.$path);
}
[$status, $body, $headers] = request('/sw.js');
verify($status === 200 && !str_contains($body, '__SMART_DRYER_BUILD__') && str_contains($headers['cache-control'] ?? '', 'no-store'), 'Versioned Service Worker bypasses HTTP cache');
verify(($headers['service-worker-allowed'] ?? '') === '/', 'Service Worker root scope');
[$status, $body, $headers] = request('/build/version.json');
$localVersion = file_get_contents(__DIR__.'/../public/build/version.json');
verify($status === 200 && json_decode($body, true) === json_decode($localVersion, true) && str_contains($headers['cache-control'] ?? '', 'no-store'), 'Web and application frontend versions match');
foreach (['/.env', '/composer.json', '/storage/probe.php'] as $path) {
    [$status] = request($path);
    verify($status === 404, 'Deny '.$path);
}

// A real WebSocket upgrade through the same proxy path used by browsers.
$socket = fsockopen('web', 80, $errno, $error, 10);
verify(is_resource($socket), 'WebSocket proxy reachable');
stream_set_timeout($socket, 10);
$key = config('reverb.apps.apps.0.key');
$path = '/app/'.rawurlencode($key).'?protocol=7&client=js&version=8.6.0&flash=false';
fwrite($socket, "GET $path HTTP/1.1\r\nHost: hanjeli-dryer.stas-rg.com\r\nOrigin: https://hanjeli-dryer.stas-rg.com\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: ".base64_encode(random_bytes(16))."\r\nSec-WebSocket-Version: 13\r\n\r\n");
$statusLine = fgets($socket);
verify(str_contains((string) $statusLine, '101 Switching Protocols'), 'Reverb WebSocket HTTP 101');
fclose($socket);

// Exercise signed server-to-server publishing through /apps as well.
$pusher = new Pusher\Pusher(config('broadcasting.connections.reverb.key'), config('broadcasting.connections.reverb.secret'), config('broadcasting.connections.reverb.app_id'), ['host' => 'web', 'port' => 80, 'scheme' => 'http', 'useTLS' => false]);
$pusher->trigger('docker-deployment-probe', 'health', ['ok' => true]);
echo "PASS: Reverb signed /apps publish\n";

// Explicitly opt-in on the isolated validation stack only.
if (getenv('HANJELI_VALIDATION') === 'true') {
    $cache = Illuminate\Support\Facades\Cache::class;
    $markerFile = storage_path('app/public/docker-persistence-probe.txt');
    if (($argv[1] ?? '') === '--persist-write') {
        $cache::forever('docker_persistence_probe', 'survived');
        file_put_contents($markerFile, 'survived');
        dispatch(static function () {
            Illuminate\Support\Facades\Cache::forever('docker_queue_probe', 'processed');
        });
        for ($i = 0; $i < 15 && $cache::get('docker_queue_probe') !== 'processed'; $i++) {
            sleep(1);
        }
        verify($cache::get('docker_queue_probe') === 'processed', 'Queue processed a real queued job');
    } elseif (($argv[1] ?? '') === '--persist-check') {
        verify($cache::get('docker_persistence_probe') === 'survived', 'MySQL data survived recreation');
        verify(file_get_contents($markerFile) === 'survived', 'Laravel storage survived recreation');
        [$status, $body] = request('/storage/docker-persistence-probe.txt');
        verify($status === 200 && $body === 'survived', 'Nginx serves persistent public uploads');
    }
}
