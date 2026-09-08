<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'is_system_active',
        'iot_mode',
        'environment_mode',
        'max_safe_temp',
        'min_safe_temp',
        'target_moisture_default',
        'sampling_interval_seconds',
        'wifi_ssid',
        'ip_address',
        'mqtt_host',
        'mqtt_port',
        'mqtt_topic',
        'whatsapp_config',
        'telegram_config',
    ];

    protected $casts = [
        'is_system_active' => 'boolean',
        'max_safe_temp' => 'float',
        'min_safe_temp' => 'float',
        'target_moisture_default' => 'float',
        'sampling_interval_seconds' => 'integer',
        'mqtt_port' => 'integer',
        'whatsapp_config' => 'array',
        'telegram_config' => 'array',
    ];
}
