<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telemetry extends Model
{
    use HasFactory;

    protected $table = 'telemetries';

    protected $fillable = [
        'batch_id',
        'temp_internal',
        'humidity_internal',
        'temp_external',
        'humidity_external',
        'solar_radiation',
        'grain_moisture',
        'weight_kg',
        'heater_status',
        'heater_level',
        'exhaust_fan_status',
        'exhaust_fan_speed',
        'recorded_at',
    ];

    protected $casts = [
        'temp_internal' => 'float',
        'humidity_internal' => 'float',
        'temp_external' => 'float',
        'humidity_external' => 'float',
        'solar_radiation' => 'float',
        'grain_moisture' => 'float',
        'weight_kg' => 'float',
        'heater_status' => 'boolean',
        'heater_level' => 'integer',
        'exhaust_fan_status' => 'boolean',
        'exhaust_fan_speed' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
}
