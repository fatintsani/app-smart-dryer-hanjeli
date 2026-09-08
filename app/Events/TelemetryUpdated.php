<?php

namespace App\Events;

use App\Models\Telemetry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelemetryUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $telemetry;
    public ?array $actuators;

    /**
     * Create a new event instance.
     */
    public function __construct(Telemetry $telemetry, ?array $actuators = null)
    {
        $d = $telemetry->recorded_at ?? $telemetry->created_at ?? now();

        $this->telemetry = [
            'id' => $telemetry->id,
            'tempInternal' => (float) $telemetry->temp_internal,
            'humidityInternal' => (float) $telemetry->humidity_internal,
            'tempExternal' => (float) $telemetry->temp_external,
            'humidityExternal' => (float) $telemetry->humidity_external,
            'solarRadiation' => (float) $telemetry->solar_radiation,
            'grainMoisture' => (float) $telemetry->grain_moisture,
            'weightCurrentKg' => (float) $telemetry->weight_kg,
            'hasData' => true,
            'timestamp' => $d->toIso8601String(),
        ];

        $this->actuators = $actuators;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('greenhouse.telemetry'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'telemetry.updated';
    }
}
