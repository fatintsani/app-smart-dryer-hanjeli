<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        'name',
        'code',
        'device_token',
        'mac_address',
        'ip_address',
        'firmware_version',
        'hardware_version',
        'target_firmware_version',
        'ota_status',
        'ota_progress',
        'last_ota_at',
        'last_ota_log',
        'purpose',
        'location',
        'category',
        'icon',
        'user_signal',
        'pin_gpio',
        'operating_range',
        'accuracy',
        'status',
        'is_active',
        'last_heartbeat',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ota_progress' => 'integer',
        'last_ota_at' => 'datetime',
    ];

    /**
     * Generate or regenerate secure device API token.
     */
    public function regenerateToken(): string
    {
        $token = 'esp32_sec_' . bin2hex(random_bytes(16));
        $this->update(['device_token' => $token]);
        return $token;
    }
}
