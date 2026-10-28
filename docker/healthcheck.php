<?php

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    Illuminate\Support\Facades\DB::select('SELECT 1');
    $kind = $argv[1] ?? '';
    if (in_array($kind, ['fpm', 'reverb'], true)) {
        $socket = @fsockopen('127.0.0.1', $kind === 'fpm' ? 9000 : 8080, $errno, $error, 3);
        if (! $socket) {
            exit(1);
        }
        fclose($socket);
    } elseif ($kind === 'mqtt') {
        // Separate ID avoids evicting the actual worker from the broker.
        config([
            'mqtt.client_id' => 'hanjeli_health_'.bin2hex(random_bytes(8)),
            'mqtt.clean_session' => true,
        ]);
        $client = $app->make(App\Services\MqttService::class)->createClient();
        $client->disconnect();
    } else {
        $running = false;
        foreach (glob('/proc/[0-9]*/cmdline') as $file) {
            $command = @file_get_contents($file);
            if ($command && str_contains($command, "artisan\0$kind")) {
                $running = true;
                break;
            }
        }
        if (! $running) {
            exit(1);
        }
    }
} catch (Throwable $e) {
    // Driver/broker exceptions can include credentials. Keep probe output private.
    exit(1);
}
