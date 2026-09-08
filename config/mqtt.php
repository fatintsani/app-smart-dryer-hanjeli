<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the MQTT broker for ESP32 & IoT Gateways.
    |
    */

    'host' => env('MQTT_HOST', 'broker.hivemq.com'),
    'port' => (int) env('MQTT_PORT', 1883),
    'client_id' => env('MQTT_CLIENT_ID', 'smartdryer_hanjeli_' . substr(md5(uniqid()), 0, 8)),
    'username' => env('MQTT_USERNAME', null),
    'password' => env('MQTT_PASSWORD', null),
    'clean_session' => env('MQTT_CLEAN_SESSION', true),
    'use_tls' => env('MQTT_USE_TLS', false),
    'keep_alive' => (int) env('MQTT_KEEP_ALIVE', 60),

    /*
    |--------------------------------------------------------------------------
    | Default Topics
    |--------------------------------------------------------------------------
    */
    'topics' => [
        'telemetry' => env('MQTT_TOPIC_TELEMETRY', 'hanjeli/greenhouse/telemetry'),
        'actuators' => env('MQTT_TOPIC_ACTUATORS', 'hanjeli/greenhouse/actuators'),
        'command' => env('MQTT_TOPIC_COMMAND', 'hanjeli/greenhouse/command'),
        'control' => env('MQTT_TOPIC_CONTROL', 'hanjeli/greenhouse/control'),
    ],
];
