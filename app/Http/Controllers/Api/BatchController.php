<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BatchCompletedMail;
use App\Models\Batch;
use App\Models\Telemetry;
use App\Models\SystemAlert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BatchController extends Controller
{
    /**
     * Get all batches with optional filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Batch::with(['operator', 'latestTelemetry'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $status = strtoupper($request->status);
            if ($status === 'ALL') {
                // all batches
            } elseif ($status === 'ACTIVE' || $status === 'RUNNING') {
                $query->whereIn('status', ['ACTIVE', 'PAUSED']);
            } elseif ($status === 'COMPLETED') {
                $query->where('status', 'COMPLETED');
            } elseif ($status === 'ABORTED' || $status === 'CANCELLED') {
                $query->where('status', 'ABORTED');
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_code', 'like', "%{$search}%")
                  ->orWhere('crop_variety', 'like', "%{$search}%")
                  ->orWhere('operator_name', 'like', "%{$search}%");
            });
        }

        $batches = $query->get()->map(function ($b) {
            return $this->formatBatch($b);
        });

        return response()->json($batches);
    }

    /**
     * Get the active drying batch.
     */
    public function activeBatch(): JsonResponse
    {
        $batch = Batch::with(['operator', 'telemetries'])
            ->whereIn('status', ['ACTIVE', 'PAUSED'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$batch) {
            return response()->json(['activeBatch' => null]);
        }

        return response()->json(['activeBatch' => $this->formatBatchDetail($batch)]);
    }

    /**
     * Get batch by ID or batch_code.
     */
    public function show(string $id): JsonResponse
    {
        $batch = Batch::with(['operator', 'telemetries', 'alerts'])
            ->where('id', $id)
            ->orWhere('batch_code', $id)
            ->first();

        if (!$batch) {
            return response()->json(['message' => 'Batch tidak ditemukan.'], 404);
        }

        return response()->json($this->formatBatchDetail($batch));
    }

    /**
     * Create / Start a new drying batch session.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cropVariety' => 'required|string|max:255',
            'initialWeightKg' => 'required|numeric|min:0.1',
            'initialMoisturePercent' => 'nullable|numeric|min:1|max:100',
            'targetMoisturePercent' => 'nullable|numeric|min:1|max:50',
            'dryingMode' => 'nullable|string|max:50',
            'trayLevel' => 'nullable|string|max:100',
            'operatorName' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Generate next batch code (e.g. HJ-2026-005)
        $year = date('Y');
        $countThisYear = Batch::where('batch_code', 'like', "HJ-{$year}-%")->count() + 1;
        $batchCode = sprintf('HJ-%s-%03d', $year, $countThisYear);

        $initialWeight = (float) $validated['initialWeightKg'];
        $initialMoisture = (float) ($validated['initialMoisturePercent'] ?? 24.5);
        $targetMoisture = (float) ($validated['targetMoisturePercent'] ?? 12.0);

        $batch = Batch::create([
            'batch_code' => $batchCode,
            'crop_variety' => $validated['cropVariety'],
            'initial_weight_kg' => $initialWeight,
            'current_weight_kg' => $initialWeight,
            'initial_moisture_percent' => $initialMoisture,
            'current_moisture_percent' => $initialMoisture,
            'target_moisture_percent' => $targetMoisture,
            'status' => 'ACTIVE',
            'drying_mode' => $validated['dryingMode'] ?? 'HYBRID_AUTO',
            'tray_level' => $validated['trayLevel'] ?? 'Semua Rak (1, 2, 3)',
            'operator_id' => $request->user()?->id,
            'operator_name' => $validated['operatorName'] ?? $request->user()?->name ?? 'Operator Green House',
            'notes' => $validated['notes'] ?? null,
            'started_at' => Carbon::now(),
            'total_duration_hours' => 0.0,
            'energy_kwh' => 0.0,
            'quality_score' => 95.0,
            'quality_grade' => 'Grade A',
        ]);

        // Insert initial telemetry point
        Telemetry::create([
            'batch_id' => $batch->id,
            'temp_internal' => 42.5,
            'humidity_internal' => 58.0,
            'temp_external' => 31.2,
            'humidity_external' => 70.0,
            'solar_radiation' => 680.0,
            'grain_moisture' => $initialMoisture,
            'weight_kg' => $initialWeight,
            'heater_status' => true,
            'heater_level' => 45,
            'exhaust_fan_status' => true,
            'exhaust_fan_speed' => 60,
            'recorded_at' => Carbon::now(),
        ]);

        // System Alert Log
        SystemAlert::create([
            'batch_id' => $batch->id,
            'level' => 'INFO',
            'category' => 'BATCH',
            'title' => 'Sesi Pengeringan Dimulai',
            'message' => "Sesi pengeringan {$batchCode} ({$batch->crop_variety}) berhasil dimulai oleh {$batch->operator_name}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sesi pengeringan {$batchCode} berhasil dimulai!",
            'batch' => $this->formatBatchDetail($batch),
        ], 201);
    }

    /**
     * Pause an active drying batch.
     */
    public function pause(string $id): JsonResponse
    {
        $batch = Batch::where('id', $id)->orWhere('batch_code', $id)->firstOrFail();

        $batch->update([
            'status' => 'PAUSED',
            'paused_at' => Carbon::now(),
        ]);

        SystemAlert::create([
            'batch_id' => $batch->id,
            'level' => 'WARNING',
            'category' => 'BATCH',
            'title' => 'Sesi Pengeringan Dijeda',
            'message' => "Sesi {$batch->batch_code} dijeda sementara oleh operator.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sesi {$batch->batch_code} berhasil dijeda.",
            'batch' => $this->formatBatchDetail($batch),
        ]);
    }

    /**
     * Resume a paused drying batch.
     */
    public function resume(string $id): JsonResponse
    {
        $batch = Batch::where('id', $id)->orWhere('batch_code', $id)->firstOrFail();

        $batch->update([
            'status' => 'ACTIVE',
            'paused_at' => null,
        ]);

        SystemAlert::create([
            'batch_id' => $batch->id,
            'level' => 'INFO',
            'category' => 'BATCH',
            'title' => 'Sesi Pengeringan Dilanjutkan',
            'message' => "Sesi {$batch->batch_code} kembali aktif melanjutkan proses pengeringan.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sesi {$batch->batch_code} kembali berjalan.",
            'batch' => $this->formatBatchDetail($batch),
        ]);
    }

    /**
     * Update an existing drying batch.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $batch = Batch::where('id', $id)->orWhere('batch_code', $id)->firstOrFail();

        $validated = $request->validate([
            'cropVariety' => 'nullable|string|max:255',
            'crop_variety' => 'nullable|string|max:255',
            'initialWeightKg' => 'nullable|numeric|min:1',
            'initial_weight_kg' => 'nullable|numeric|min:1',
            'initialMoisturePercent' => 'nullable|numeric|min:5|max:50',
            'initial_moisture_percent' => 'nullable|numeric|min:5|max:50',
            'targetMoisturePercent' => 'nullable|numeric|min:5|max:30',
            'target_moisture_percent' => 'nullable|numeric|min:5|max:30',
            'finalMoisturePercent' => 'nullable|numeric|min:5|max:30',
            'final_moisture_percent' => 'nullable|numeric|min:5|max:30',
            'finalWeightKg' => 'nullable|numeric|min:1',
            'final_weight_kg' => 'nullable|numeric|min:1',
            'dryingMode' => 'nullable|string|max:100',
            'drying_mode' => 'nullable|string|max:100',
            'operatorName' => 'nullable|string|max:255',
            'operator_name' => 'nullable|string|max:255',
            'qualityGrade' => 'nullable|string|max:100',
            'quality_grade' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $updateData = [];
        if (isset($validated['cropVariety']) || isset($validated['crop_variety'])) {
            $updateData['crop_variety'] = $validated['cropVariety'] ?? $validated['crop_variety'];
        }
        if (isset($validated['initialWeightKg']) || isset($validated['initial_weight_kg'])) {
            $updateData['initial_weight_kg'] = $validated['initialWeightKg'] ?? $validated['initial_weight_kg'];
        }
        if (isset($validated['initialMoisturePercent']) || isset($validated['initial_moisture_percent'])) {
            $updateData['initial_moisture_percent'] = $validated['initialMoisturePercent'] ?? $validated['initial_moisture_percent'];
        }
        if (isset($validated['targetMoisturePercent']) || isset($validated['target_moisture_percent'])) {
            $updateData['target_moisture_percent'] = $validated['targetMoisturePercent'] ?? $validated['target_moisture_percent'];
        }
        if (isset($validated['finalMoisturePercent']) || isset($validated['final_moisture_percent'])) {
            $updateData['final_moisture_percent'] = $validated['finalMoisturePercent'] ?? $validated['final_moisture_percent'];
        }
        if (isset($validated['finalWeightKg']) || isset($validated['final_weight_kg'])) {
            $updateData['final_weight_kg'] = $validated['finalWeightKg'] ?? $validated['final_weight_kg'];
        }
        if (isset($validated['dryingMode']) || isset($validated['drying_mode'])) {
            $updateData['drying_mode'] = $validated['dryingMode'] ?? $validated['drying_mode'];
        }
        if (isset($validated['operatorName']) || isset($validated['operator_name'])) {
            $updateData['operator_name'] = $validated['operatorName'] ?? $validated['operator_name'];
        }
        if (isset($validated['qualityGrade']) || isset($validated['quality_grade'])) {
            $updateData['quality_grade'] = $validated['qualityGrade'] ?? $validated['quality_grade'];
        }
        if (array_key_exists('notes', $validated)) {
            $updateData['notes'] = $validated['notes'];
        }

        $batch->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Data batch {$batch->batch_code} berhasil diperbarui.",
            'batch' => $this->formatBatchDetail($batch),
        ]);
    }

    /**
     * Complete a drying batch.
     */
    public function complete(Request $request, string $id): JsonResponse
    {
        $batch = Batch::where('id', $id)->orWhere('batch_code', $id)->firstOrFail();

        $now = Carbon::now();
        $startedAt = $batch->started_at ?? $now->copy()->subHours(10);
        $durationHours = round(max(0.5, $startedAt->diffInMinutes($now) / 60), 1);
        $finalMoisture = $request->finalMoisturePercent ?? $batch->current_moisture_percent ?? $batch->target_moisture_percent ?? 12.0;
        $finalWeight = $request->finalWeightKg ?? ($batch->initial_weight_kg * (1 - (($batch->initial_moisture_percent - $finalMoisture) / 100)));
        $energyKwh = round($durationHours * 1.5, 1);

        // Compute quality grade
        $qualityScore = 95.0;
        $qualityGrade = 'Grade A (Ekspor)';
        if ($finalMoisture > 14.0) {
            $qualityScore = 80.0;
            $qualityGrade = 'Grade B (Standar Lokal)';
        }

        $batch->update([
            'status' => 'COMPLETED',
            'completed_at' => $now,
            'final_moisture_percent' => $finalMoisture,
            'current_moisture_percent' => $finalMoisture,
            'final_weight_kg' => round($finalWeight, 2),
            'current_weight_kg' => round($finalWeight, 2),
            'total_duration_hours' => $durationHours,
            'energy_kwh' => $energyKwh,
            'quality_score' => $qualityScore,
            'quality_grade' => $qualityGrade,
        ]);

        $batch->refresh();

        SystemAlert::create([
            'batch_id' => $batch->id,
            'level' => 'INFO',
            'category' => 'BATCH',
            'title' => 'Sesi Pengeringan Selesai',
            'message' => "Sesi {$batch->batch_code} selesai dengan kadar air akhir {$finalMoisture}%. Kualitas: {$qualityGrade}.",
        ]);

        // Send Batch Completion Email Report
        try {
            $recipients = User::pluck('email')->filter()->all();
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BatchCompletedMail($batch));
            }
        } catch (\Throwable $e) {
            Log::warning('Batch completed email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Sesi pengeringan {$batch->batch_code} telah selesai dan tersimpan ke riwayat!",
            'batch' => $this->formatBatchDetail($batch),
        ]);
    }

    /**
     * Get export data (for CSV / certificate download).
     */
    public function export(string $id): JsonResponse
    {
        $batch = Batch::with(['operator', 'telemetries', 'alerts'])
            ->where('id', $id)
            ->orWhere('batch_code', $id)
            ->firstOrFail();

        return response()->json([
            'batchCode' => $batch->batch_code,
            'cropVariety' => $batch->crop_variety,
            'operatorName' => $batch->operator_name ?? $batch->operator?->name ?? 'Operator Green House',
            'startedAt' => $batch->started_at?->toIso8601String(),
            'completedAt' => $batch->completed_at?->toIso8601String(),
            'durationHours' => $batch->total_duration_hours,
            'initialWeightKg' => $batch->initial_weight_kg,
            'finalWeightKg' => $batch->final_weight_kg ?? $batch->current_weight_kg,
            'initialMoisturePercent' => $batch->initial_moisture_percent,
            'finalMoisturePercent' => $batch->final_moisture_percent ?? $batch->current_moisture_percent,
            'targetMoisturePercent' => $batch->target_moisture_percent,
            'energyUsedKwh' => $batch->energy_kwh,
            'qualityScore' => $batch->quality_score,
            'qualityGrade' => $batch->quality_grade,
            'telemetryLogCount' => $batch->telemetries->count(),
            'telemetryLogs' => $batch->telemetries->map(function ($t) {
                return [
                    'time' => $t->recorded_at->toIso8601String(),
                    'tempInternal' => $t->temp_internal,
                    'humidityInternal' => $t->humidity_internal,
                    'tempExternal' => $t->temp_external,
                    'humidityExternal' => $t->humidity_external,
                    'solarRadiation' => $t->solar_radiation,
                    'grainMoisture' => $t->grain_moisture,
                    'weightKg' => $t->weight_kg,
                    'heaterStatus' => $t->heater_status ? 'ON' : 'OFF',
                    'exhaustFanSpeed' => $t->exhaust_fan_speed . '%',
                ];
            }),
        ]);
    }

    /**
     * Format a batch for list responses.
     */
    private function formatBatch($b): array
    {
        return [
            'id' => $b->id,
            'batchCode' => $b->batch_code,
            'cropVariety' => $b->crop_variety,
            'status' => $b->status,
            'dryingMode' => $b->drying_mode,
            'trayLevel' => $b->tray_level,
            'initialWeightKg' => $b->initial_weight_kg,
            'currentWeightKg' => $b->current_weight_kg,
            'finalWeightKg' => $b->final_weight_kg,
            'initialMoisturePercent' => $b->initial_moisture_percent,
            'currentMoisturePercent' => $b->current_moisture_percent,
            'finalMoisturePercent' => $b->final_moisture_percent,
            'targetMoisturePercent' => $b->target_moisture_percent,
            'totalDurationHours' => $b->total_duration_hours,
            'energyKwh' => $b->energy_kwh,
            'qualityScore' => $b->quality_score,
            'qualityGrade' => $b->quality_grade,
            'operator' => [
                'name' => $b->operator_name ?? $b->operator?->name ?? 'Operator Green House',
                'email' => $b->operator?->email ?? 'operator@hanjeli.com',
            ],
            'startedAt' => $b->started_at?->toIso8601String(),
            'completedAt' => $b->completed_at?->toIso8601String(),
            'createdAt' => $b->created_at?->toIso8601String(),
        ];
    }

    /**
     * Format a batch with full relations for detail views.
     */
    private function formatBatchDetail($b): array
    {
        $formatted = $this->formatBatch($b);
        $formatted['notes'] = $b->notes;
        $formatted['telemetry'] = $b->telemetries->map(function ($t) {
            return [
                'id' => $t->id,
                'tempInternal' => $t->temp_internal,
                'humidityInternal' => $t->humidity_internal,
                'tempExternal' => $t->temp_external,
                'humidityExternal' => $t->humidity_external,
                'solarRadiation' => $t->solar_radiation,
                'grainMoisture' => $t->grain_moisture,
                'weightKg' => $t->weight_kg,
                'heaterStatus' => $t->heater_status,
                'heaterLevel' => $t->heater_level,
                'exhaustFanStatus' => $t->exhaust_fan_status,
                'exhaustFanSpeed' => $t->exhaust_fan_speed,
                'timestamp' => $t->recorded_at?->toIso8601String() ?? $t->created_at?->toIso8601String(),
            ];
        });
        $formatted['alerts'] = $b->alerts->map(function ($a) {
            return [
                'id' => $a->id,
                'level' => $a->level,
                'category' => $a->category,
                'title' => $a->title,
                'message' => $a->message,
                'createdAt' => $a->created_at?->toIso8601String(),
            ];
        });

        return $formatted;
    }
}
