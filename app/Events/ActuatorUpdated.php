<?php

namespace App\Events;

use App\Models\ActuatorState;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActuatorUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $actuators;

    /**
     * Create a new event instance.
     */
    public function __construct(ActuatorState $state)
    {
        $this->actuators = [
            'exhaustFanStatus' => (bool) $state->exhaust_fan_status,
            'exhaustFanSpeed' => (int) $state->exhaust_fan_speed,
            'intakeFanStatus' => (bool) $state->intake_fan_status,
            'circFanStatus' => (bool) $state->circ_fan_status,
            'auxHeaterStatus' => (bool) $state->aux_heater_status,
            'heaterStatus' => (bool) $state->aux_heater_status,
            'heaterCh1Status' => (bool) ($state->heater_ch1_status ?? $state->aux_heater_status),
            'heaterCh2Status' => (bool) ($state->heater_ch2_status ?? $state->aux_heater2_status ?? $state->aux_heater_status),
            'auxHeater2Status' => (bool) ($state->aux_heater2_status ?? $state->heater_ch2_status ?? $state->aux_heater_status),
            'auxHeaterLevel' => (int) $state->aux_heater_level,
            'isOverrideActive' => (bool) $state->is_override_active,
            'overrideMode' => (string) $state->override_mode,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('greenhouse.actuators'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'actuators.updated';
    }
}
