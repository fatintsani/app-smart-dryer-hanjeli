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
    ];
}
