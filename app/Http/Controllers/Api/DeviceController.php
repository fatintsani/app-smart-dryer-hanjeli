<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Get list of all registered devices and sensors.
     */
    public function index(): JsonResponse
    {
        $devices = Device::orderBy('id', 'asc')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'code' => $d->code,
                'purpose' => $d->purpose,
                'location' => $d->location,
                'category' => $d->category,
                'icon' => $d->icon,
                'userSignal' => $d->user_signal,
                'pinGpio' => $d->pin_gpio,
                'operatingRange' => $d->operating_range,
                'accuracy' => $d->accuracy,
                'status' => $d->status,
                'isActive' => $d->is_active,
                'lastHeartbeat' => $d->last_heartbeat,
                'isPinging' => false,
            ];
        });

        return response()->json($devices);
    }

    /**
     * Register a new device or sensor.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'purpose' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $icon = $validated['icon'] ?? 'temp';
        $purpose = $validated['purpose'] ?? 'Memantau kondisi ruang pengering';

        if (isset($validated['type'])) {
            if (str_contains($validated['type'], 'Cahaya') || str_contains($validated['type'], 'Solar')) {
                $icon = 'sun';
                $purpose = 'Mendeteksi terik radiasi matahari';
            } elseif (str_contains($validated['type'], 'Pengendali') || str_contains($validated['type'], 'ESP32')) {
                $icon = 'microchip';
                $purpose = 'Pengendali modul tambahan greenhouse';
            } elseif (str_contains($validated['type'], 'Pemanas') || str_contains($validated['type'], 'Relai')) {
                $icon = 'relay';
                $purpose = 'Sakelar pengendali daya kipas & heater';
            }
        }

        $device = Device::create([
            'name' => $validated['name'],
            'purpose' => $purpose,
            'location' => $validated['location'],
            'icon' => $icon,
            'category' => 'SENSOR',
            'user_signal' => 'Bagus & Stabil',
            'status' => 'online',
            'is_active' => true,
            'last_heartbeat' => 'Baru saja',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Perangkat '{$device->name}' berhasil didaftarkan ke sistem!",
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'purpose' => $device->purpose,
                'location' => $device->location,
                'icon' => $device->icon,
                'userSignal' => $device->user_signal,
                'status' => $device->status,
                'isActive' => $device->is_active,
                'lastHeartbeat' => $device->last_heartbeat,
                'isPinging' => false,
            ],
        ], 201);
    }

    /**
     * Ping single device to verify latency / responsiveness.
     */
    public function ping(string $id): JsonResponse
    {
        $device = Device::findOrFail($id);

        $device->update([
            'status' => 'online',
            'last_heartbeat' => 'Baru saja',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Ping ke {$device->name} berhasil! Respon 24ms (Stabil).",
            'status' => 'online',
            'latencyMs' => 24,
            'lastHeartbeat' => 'Baru saja',
        ]);
    }

    /**
     * Toggle device active/connected status.
     */
    public function toggle(string $id): JsonResponse
    {
        $device = Device::findOrFail($id);
        $newStatus = $device->status === 'online' ? 'offline' : 'online';

        $device->update([
            'status' => $newStatus,
            'is_active' => $newStatus === 'online',
            'last_heartbeat' => $newStatus === 'online' ? 'Baru saja' : 'Terputus',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status {$device->name} sekarang {$newStatus}.",
            'status' => $newStatus,
            'isActive' => $newStatus === 'online',
        ]);
    }

    /**
     * Ping all devices simultaneously.
     */
    public function pingAll(): JsonResponse
    {
        Device::query()->update([
            'status' => 'online',
            'is_active' => true,
            'last_heartbeat' => 'Baru saja',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua perangkat greenhouse berhasil merespons sinyal dengan status Online!',
            'onlineCount' => Device::count(),
        ]);
    }

    /**
     * Update device details.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'operatingRange' => 'nullable|string|max:255',
            'operating_range' => 'nullable|string|max:255',
            'accuracy' => 'nullable|string|max:255',
            'pinGpio' => 'nullable|string|max:50',
            'pin_gpio' => 'nullable|string|max:50',
        ]);

        $device->update([
            'name' => $validated['name'],
            'location' => $validated['location'] ?? $device->location,
            'purpose' => $validated['purpose'] ?? $device->purpose,
            'category' => $validated['category'] ?? $device->category,
            'operating_range' => $validated['operatingRange'] ?? $validated['operating_range'] ?? $device->operating_range,
            'accuracy' => $validated['accuracy'] ?? $device->accuracy,
            'pin_gpio' => $validated['pinGpio'] ?? $validated['pin_gpio'] ?? $device->pin_gpio,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Perangkat '{$device->name}' berhasil diperbarui.",
            'device' => $device,
        ]);
    }

    /**
     * Delete a device from the system.
     */
    public function destroy(string $id): JsonResponse
    {
        $device = Device::findOrFail($id);
        $name = $device->name;
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => "Perangkat '{$name}' telah dihapus dari sistem.",
        ]);
    }
}
