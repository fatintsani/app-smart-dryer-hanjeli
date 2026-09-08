<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActuatorState extends Model
{
    use HasFactory;

    protected $table = 'actuator_states';

    protected $fillable = [
        'exhaust_fan_status',
        'exhaust_fan_speed',
        'intake_fan_status',
        'circ_fan_status',
        'aux_heater_status',
        'aux_heater_level',
        'is_override_active',
        'override_mode',
        'updated_by',
    ];

    protected $casts = [
        'exhaust_fan_status' => 'boolean',
        'exhaust_fan_speed' => 'integer',
        'intake_fan_status' => 'boolean',
        'circ_fan_status' => 'boolean',
        'aux_heater_status' => 'boolean',
        'aux_heater_level' => 'integer',
        'is_override_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
