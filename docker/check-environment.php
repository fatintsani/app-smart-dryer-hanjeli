<?php

// Safe preflight: report names/recommendations only, never actual secret values.
$expected = [
    'APP_ENV' => 'production', 'APP_DEBUG' => 'false',
    'APP_URL' => 'https://hanjeli-dryer.stas-rg.com', 'APP_TIMEZONE' => 'Asia/Jakarta',
    'DB_CONNECTION' => 'mysql', 'DB_HOST' => 'db', 'DB_PORT' => '3306',
    'DB_DATABASE' => 'dryer', 'DB_USERNAME' => 'hanjeli_app',
    'BROADCAST_CONNECTION' => 'reverb', 'REVERB_HOST' => 'reverb',
    'REVERB_PORT' => '8080', 'REVERB_SCHEME' => 'http',
    'QUEUE_CONNECTION' => 'database', 'SESSION_DRIVER' => 'database',
    'CACHE_STORE' => 'database', 'LOG_CHANNEL' => 'stderr',
    'VITE_REVERB_HOST' => 'hanjeli-dryer.stas-rg.com',
    'VITE_REVERB_PORT' => '443', 'VITE_REVERB_SCHEME' => 'https',
];
$errors = [];
foreach ($expected as $key => $value) {
    if (getenv($key) !== $value) {
        $errors[] = "$key=$value";
    }
}
foreach (['APP_KEY', 'DB_PASSWORD', 'MYSQL_ROOT_PASSWORD', 'REVERB_APP_ID', 'REVERB_APP_KEY', 'REVERB_APP_SECRET', 'MQTT_HOST'] as $key) {
    if (! getenv($key)) {
        $errors[] = "$key: required (value hidden)";
    }
}
if (in_array(strtolower((string) getenv('DB_PASSWORD')), ['', 'root', 'password', 'changeme'], true)
    || strlen((string) getenv('DB_PASSWORD')) < 20) {
    $errors[] = 'DB_PASSWORD: use a unique random password of at least 20 characters';
}
if (strlen((string) getenv('MYSQL_ROOT_PASSWORD')) < 20
    || getenv('MYSQL_ROOT_PASSWORD') === getenv('DB_PASSWORD')) {
    $errors[] = 'MYSQL_ROOT_PASSWORD: use a separate random password of at least 20 characters';
}
foreach (['DB_URL', 'DB_SOCKET'] as $key) {
    if (getenv($key)) {
        $errors[] = "$key: leave empty to use Docker database settings";
    }
}
if (getenv('VITE_REVERB_APP_KEY') !== getenv('REVERB_APP_KEY')) {
    $errors[] = 'VITE_REVERB_APP_KEY: must match REVERB_APP_KEY (values hidden)';
}
$built = json_decode(file_get_contents(__DIR__.'/../public/build/deployment.json'), true, flags: JSON_THROW_ON_ERROR);
foreach ($built as $key => $value) {
    $default = match ($key) {
        'VITE_APP_NAME' => 'Smart_Dryer_Hanjeli',
        'VITE_API_BASE_URL' => '/api',
        default => '',
    };
    if ((getenv($key) ?: $default) !== $value) {
        $errors[] = "$key: rebuild both app/web images after changing this public setting";
    }
}
if ($errors) {
    fwrite(STDERR, "Docker environment preflight failed:\n- ".implode("\n- ", array_unique($errors))."\n");
    exit(1);
}
