<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'crop_variety',
        'initial_weight_kg',
        'current_weight_kg',
        'final_weight_kg',
        'initial_moisture_percent',
        'current_moisture_percent',
        'final_moisture_percent',
        'target_moisture_percent',
        'status',
        'drying_mode',
        'tray_level',
        'operator_id',
        'operator_name',
        'notes',
        'started_at',
        'paused_at',
        'completed_at',
        'total_duration_hours',
        'energy_kwh',
        'quality_score',
        'quality_grade',
    ];

    protected $casts = [
        'initial_weight_kg' => 'float',
        'current_weight_kg' => 'float',
        'final_weight_kg' => 'float',
        'initial_moisture_percent' => 'float',
        'current_moisture_percent' => 'float',
        'final_moisture_percent' => 'float',
        'target_moisture_percent' => 'float',
        'total_duration_hours' => 'float',
        'energy_kwh' => 'float',
        'quality_score' => 'float',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function telemetries(): HasMany
    {
        return $this->hasMany(Telemetry::class, 'batch_id')->orderBy('recorded_at', 'asc');
    }

    public function latestTelemetry()
    {
        return $this->hasOne(Telemetry::class, 'batch_id')->latestOfMany('recorded_at');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(SystemAlert::class, 'batch_id')->orderBy('created_at', 'desc');
    }
}
