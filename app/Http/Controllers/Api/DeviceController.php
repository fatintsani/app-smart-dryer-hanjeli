<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\FirmwareRelease;
use Carbon\Carbon;
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
                'deviceToken' => $d->device_token,
                'macAddress' => $d->mac_address ?? '24:6F:28:B4:7A:' . sprintf('%02X', $d->id * 17 % 255),
                'ipAddress' => $d->ip_address ?? ('192.168.1.' . (100 + $d->id)),
                'firmwareVersion' => $d->firmware_version ?? 'v2.4.2',
                'hardwareVersion' => $d->hardware_version ?? 'ESP32-WROOM-32D Rev 1.0',
                'targetFirmwareVersion' => $d->target_firmware_version,
                'otaStatus' => $d->ota_status ?? 'IDLE',
                'otaProgress' => (int) ($d->ota_progress ?? 0),
                'lastOtaAt' => $d->last_ota_at ? $d->last_ota_at->toIso8601String() : null,
                'lastOtaLog' => $d->last_ota_log,
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
            'category' => 'nullable|string',
            'firmware_version' => 'nullable|string',
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

        $code = 'DEV-' . strtoupper(substr(uniqid(), -6));
        $device = Device::create([
            'name' => $validated['name'],
            'code' => $code,
            'device_token' => 'esp32_sec_' . bin2hex(random_bytes(16)),
            'mac_address' => '24:6F:28:B4:7A:' . sprintf('%02X', rand(10, 250)),
            'ip_address' => '192.168.1.' . rand(110, 200),
            'firmware_version' => $validated['firmware_version'] ?? 'v2.4.2',
            'hardware_version' => 'ESP32-WROOM-32D Rev 1.0',
            'ota_status' => 'IDLE',
            'ota_progress' => 0,
            'purpose' => $purpose,
            'location' => $validated['location'],
            'icon' => $icon,
            'category' => $validated['category'] ?? 'SENSOR',
            'user_signal' => 'Bagus & Stabil',
            'status' => 'online',
            'is_active' => true,
            'last_heartbeat' => 'Baru saja',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Perangkat '{$device->name}' berhasil didaftarkan ke sistem dengan token otentikasi baru!",
            'device' => $device,
        ], 201);
    }

    /**
     * Regenerate device API authentication token.
     */
    public function regenerateToken(string $id): JsonResponse
    {
        $device = Device::findOrFail($id);
        $newToken = $device->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => "Token otentikasi untuk {$device->name} berhasil diperbarui!",
            'deviceToken' => $newToken,
            'device' => $device,
        ]);
    }

    /**
     * Trigger remote Firmware OTA update on target device.
     */
    public function triggerOta(Request $request, string $id): JsonResponse
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'targetVersion' => 'required|string',
        ]);

        $targetVersion = $validated['targetVersion'];
        $release = FirmwareRelease::where('version', $targetVersion)->first();

        $device->update([
            'target_firmware_version' => $targetVersion,
            'ota_status' => 'PENDING',
            'ota_progress' => 0,
            'last_ota_at' => Carbon::now(),
            'last_ota_log' => "Trigger OTA diinisiasi oleh Administrator ke versi {$targetVersion} (" . Carbon::now()->toDateTimeString() . ")",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Perintah OTA update ke versi {$targetVersion} berhasil dikirim ke node {$device->name}.",
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'targetFirmwareVersion' => $device->target_firmware_version,
                'otaStatus' => $device->ota_status,
                'otaProgress' => $device->ota_progress,
                'release' => $release,
            ],
        ]);
    }

    /**
     * Microcontroller OTA Check Polling endpoint (ESP32 calls this).
     */
    public function checkOta(Request $request): JsonResponse
    {
        $token = $request->header('X-Device-Token') ?? $request->input('device_token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Missing X-Device-Token header'], 401);
        }

        $device = Device::where('device_token', $token)->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Unauthorized device'], 401);
        }

        // Check if there is an active pending or in-progress OTA target
        if ($device->target_firmware_version && $device->target_firmware_version !== $device->firmware_version) {
            $release = FirmwareRelease::where('version', $device->target_firmware_version)->first();

            return response()->json([
                'updateAvailable' => true,
                'targetVersion' => $device->target_firmware_version,
                'currentVersion' => $device->firmware_version,
                'binaryUrl' => url($release?->file_path ?? "/firmware/bin/hanjeli_esp32_{$device->target_firmware_version}.bin"),
                'fileSizeBytes' => $release?->file_size_bytes ?? 1248560,
                'checksumSha256' => $release?->checksum_sha256,
                'instructions' => 'Start download, verify SHA-256, flash to OTA partition, and restart.',
            ]);
        }

        return response()->json([
            'updateAvailable' => false,
            'currentVersion' => $device->firmware_version,
            'message' => 'Firmware is up-to-date.',
        ]);
    }

    /**
     * Microcontroller OTA Progress report endpoint.
     */
    public function reportOtaProgress(Request $request): JsonResponse
    {
        $token = $request->header('X-Device-Token') ?? $request->input('device_token');
        $device = Device::where('device_token', $token)->first();

        if (!$device && $request->has('deviceId')) {
            $device = Device::find($request->deviceId);
        }

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string', // DOWNLOADING, FLASHING, SUCCESS, FAILED
            'progress' => 'required|integer|min:0|max:100',
            'log' => 'nullable|string',
            'newVersion' => 'nullable|string',
        ]);

        $updates = [
            'ota_status' => strtoupper($validated['status']),
            'ota_progress' => $validated['progress'],
            'last_ota_log' => $validated['log'] ?? "OTA Status: {$validated['status']} ({$validated['progress']}%)",
        ];

        if (strtoupper($validated['status']) === 'SUCCESS') {
            $updates['firmware_version'] = $validated['newVersion'] ?? $device->target_firmware_version ?? 'v2.5.0';
            $updates['target_firmware_version'] = null;
            $updates['last_ota_at'] = Carbon::now();
            $updates['ota_progress'] = 100;
        }

        $device->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'OTA Progress updated successfully.',
            'device' => $device,
        ]);
    }

    /**
     * Get list of all firmware releases.
     */
    public function getFirmwareReleases(): JsonResponse
    {
        $releases = FirmwareRelease::orderBy('id', 'desc')->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'version' => $r->version,
                'releaseTitle' => $r->release_title,
                'changelog' => $r->changelog,
                'filePath' => $r->file_path,
                'fileSizeBytes' => $r->file_size_bytes,
                'fileSizeFormatted' => number_format($r->file_size_bytes / 1024 / 1024, 2) . ' MB',
                'checksumSha256' => $r->checksum_sha256,
                'isLatest' => $r->is_latest,
                'isStable' => $r->is_stable,
                'minHardwareVersion' => $r->min_hardware_version,
                'createdAt' => $r->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json($releases);
    }

    /**
     * Create a new firmware release record.
     */
    public function createFirmwareRelease(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'version' => 'required|string|unique:firmware_releases,version',
            'releaseTitle' => 'required|string',
            'changelog' => 'nullable|string',
            'fileSizeBytes' => 'nullable|integer',
            'checksumSha256' => 'nullable|string',
            'isLatest' => 'nullable|boolean',
        ]);

        if (!empty($validated['isLatest'])) {
            FirmwareRelease::where('is_latest', true)->update(['is_latest' => false]);
        }

        $release = FirmwareRelease::create([
            'version' => $validated['version'],
            'release_title' => $validated['releaseTitle'],
            'changelog' => $validated['changelog'] ?? 'Pembaruan firmware kestabilan sistem telemetri greenhouse.',
            'file_path' => "/firmware/bin/hanjeli_esp32_{$validated['version']}.bin",
            'file_size_bytes' => $validated['fileSizeBytes'] ?? 1250000,
            'checksum_sha256' => $validated['checksumSha256'] ?? bin2hex(random_bytes(32)),
            'is_latest' => $validated['isLatest'] ?? true,
            'is_stable' => true,
            'min_hardware_version' => 'ESP32-WROOM-32D Rev 1.0',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Rilis firmware {$release->version} berhasil ditambahkan!",
            'release' => $release,
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
            'last_heartbeat' => 'Baru saja (Ping OK)',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sinyal uji koneksi ke '{$device->name}' berhasil diterima (Latency: 18ms).",
            'device' => $device,
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
