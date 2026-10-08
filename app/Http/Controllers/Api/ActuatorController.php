<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActuatorState;
use App\Models\SystemAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActuatorController extends Controller
{
    /**
     * Get current actuator statuses.
     */
    public function status(): JsonResponse
    {
        $state = ActuatorState::firstOrCreate([], [
            'exhaust_fan_status' => true,
            'exhaust_fan_speed' => 60,
            'intake_fan_status' => true,
            'circ_fan_status' => true,
            'aux_heater_status' => true,
            'aux_heater_level' => 45,
            'uv_light_status' => false,
            'rotary_tray_status' => true,
            'dehumidifier_status' => true,
            'is_override_active' => false,
            'override_mode' => 'AUTO',
        ]);

        return response()->json([
            'exhaustFanStatus' => $state->exhaust_fan_status,
            'exhaustFanSpeed' => $state->exhaust_fan_speed,
            'intakeFanStatus' => $state->intake_fan_status,
            'circFanStatus' => $state->circ_fan_status,
            'auxHeaterStatus' => $state->aux_heater_status,
            'auxHeaterLevel' => $state->aux_heater_level,
            'uvLightStatus' => $state->uv_light_status ?? false,
            'rotaryTrayStatus' => $state->rotary_tray_status ?? false,
            'dehumidifierStatus' => $state->dehumidifier_status ?? false,
            'isOverrideActive' => $state->is_override_active,
            'overrideMode' => $state->override_mode,
        ]);
    }

    /**
     * Control actuators / toggle overrides.
     */
    public function control(Request $request, \App\Services\MqttService $mqttService): JsonResponse
    {
        $state = ActuatorState::firstOrCreate([], [
            'exhaust_fan_status' => true,
            'exhaust_fan_speed' => 60,
            'intake_fan_status' => true,
            'circ_fan_status' => true,
            'aux_heater_status' => true,
            'aux_heater_level' => 45,
            'uv_light_status' => false,
            'rotary_tray_status' => true,
            'dehumidifier_status' => true,
            'is_override_active' => false,
            'override_mode' => 'AUTO',
        ]);

        $updates = [];

        if ($request->has('exhaustFanStatus')) $updates['exhaust_fan_status'] = (bool) $request->exhaustFanStatus;
        if ($request->has('exhaustFanSpeed')) $updates['exhaust_fan_speed'] = (int) $request->exhaustFanSpeed;
        if ($request->has('intakeFanStatus')) $updates['intake_fan_status'] = (bool) $request->intakeFanStatus;
        if ($request->has('circFanStatus')) $updates['circ_fan_status'] = (bool) $request->circFanStatus;
        if ($request->has('auxHeaterStatus')) $updates['aux_heater_status'] = (bool) $request->auxHeaterStatus;
        if ($request->has('auxHeaterLevel')) $updates['aux_heater_level'] = (int) $request->auxHeaterLevel;
        if ($request->has('uvLightStatus')) $updates['uv_light_status'] = (bool) $request->uvLightStatus;
        if ($request->has('rotaryTrayStatus')) $updates['rotary_tray_status'] = (bool) $request->rotaryTrayStatus;
        if ($request->has('dehumidifierStatus')) $updates['dehumidifier_status'] = (bool) $request->dehumidifierStatus;
        if ($request->has('isOverrideActive')) $updates['is_override_active'] = (bool) $request->isOverrideActive;
        if ($request->has('overrideMode')) $updates['override_mode'] = $request->overrideMode;

        if (!empty($updates)) {
            $updates['updated_by'] = $request->user()?->id;
            $state->update($updates);

            if (isset($updates['is_override_active']) && $updates['is_override_active']) {
                $alert = SystemAlert::create([
                    'level' => 'WARNING',
                    'category' => 'ACTUATOR',
                    'title' => 'Override Manual Aktif',
                    'message' => 'Kontrol darurat aktuator telah diaktifkan secara manual oleh operator.',
                ]);
                event(new \App\Events\AlertTriggered($alert));
            }

            // Real-Time Broadcast to WebSockets
            event(new \App\Events\ActuatorUpdated($state));

            // Publish to MQTT Broker for hardware actuators
            $actuatorData = [
                'exhaustFanStatus' => $state->exhaust_fan_status,
                'exhaustFanSpeed' => $state->exhaust_fan_speed,
                'intakeFanStatus' => $state->intake_fan_status,
                'circFanStatus' => $state->circ_fan_status,
                'auxHeaterStatus' => $state->aux_heater_status,
                'auxHeaterLevel' => $state->aux_heater_level,
                'uvLightStatus' => $state->uv_light_status,
                'rotaryTrayStatus' => $state->rotary_tray_status,
                'dehumidifierStatus' => $state->dehumidifier_status,
                'isOverrideActive' => $state->is_override_active,
                'overrideMode' => $state->override_mode,
            ];
            $mqttService->publishControl($actuatorData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status aktuator berhasil diperbarui.',
            'actuators' => [
                'exhaustFanStatus' => $state->exhaust_fan_status,
                'exhaustFanSpeed' => $state->exhaust_fan_speed,
                'intakeFanStatus' => $state->intake_fan_status,
                'circFanStatus' => $state->circ_fan_status,
                'auxHeaterStatus' => $state->aux_heater_status,
                'auxHeaterLevel' => $state->aux_heater_level,
                'uvLightStatus' => $state->uv_light_status,
                'rotaryTrayStatus' => $state->rotary_tray_status,
                'dehumidifierStatus' => $state->dehumidifier_status,
                'isOverrideActive' => $state->is_override_active,
                'overrideMode' => $state->override_mode,
            ],
        ]);
    }
}
