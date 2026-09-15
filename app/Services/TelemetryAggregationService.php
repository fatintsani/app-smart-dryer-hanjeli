<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Telemetry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelemetryAggregationService
{
    /**
     * Downsample telemetry data for a specific batch into bucket intervals.
     *
     * @param Batch $batch
     * @param int $intervalMinutes Bucket size in minutes (e.g. 5, 15, 60)
     * @param bool $force Force downsampling even if already downsampled
     * @return array Summary of downsampling operation
     */
    public function downsampleBatch(Batch $batch, int $intervalMinutes = 5, bool $force = false): array
    {
        $batchId = $batch->id;
        $totalRawRows = Telemetry::where('batch_id', $batchId)->count();

        // If there are too few rows or empty, skip
        if ($totalRawRows <= 5 && !$force) {
            return [
                'batchId' => $batchId,
                'batchCode' => $batch->batch_code,
                'status' => 'SKIPPED',
                'reason' => 'Data telemetry terlalu sedikit (' . $totalRawRows . ' baris), tidak perlu downsampling.',
                'originalRows' => $totalRawRows,
                'downsampledRows' => $totalRawRows,
                'savedRows' => 0,
                'reductionPercentage' => 0.0,
            ];
        }

        $intervalSeconds = $intervalMinutes * 60;

        // Fetch all raw telemetry records ordered by recorded_at
        $rawRecords = Telemetry::where('batch_id', $batchId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        if ($rawRecords->isEmpty()) {
            return [
                'batchId' => $batchId,
                'batchCode' => $batch->batch_code,
                'status' => 'SKIPPED',
                'reason' => 'Tidak ada data telemetry untuk batch ini.',
                'originalRows' => 0,
                'downsampledRows' => 0,
                'savedRows' => 0,
                'reductionPercentage' => 0.0,
            ];
        }

        // Group records by time bucket using PHP (database-driver agnostic)
        $buckets = [];
        foreach ($rawRecords as $record) {
            $timestamp = $record->recorded_at ? $record->recorded_at->timestamp : $record->created_at->timestamp;
            $bucketKey = (int) (floor($timestamp / $intervalSeconds) * $intervalSeconds);

            if (!isset($buckets[$bucketKey])) {
                $buckets[$bucketKey] = [
                    'temp_internal' => [],
                    'humidity_internal' => [],
                    'temp_external' => [],
                    'humidity_external' => [],
                    'solar_radiation' => [],
                    'grain_moisture' => [],
                    'weight_kg' => [],
                    'heater_status' => false,
                    'heater_level' => [],
                    'exhaust_fan_status' => false,
                    'exhaust_fan_speed' => [],
                    'count' => 0,
                ];
            }

            $buckets[$bucketKey]['temp_internal'][] = (float) $record->temp_internal;
            $buckets[$bucketKey]['humidity_internal'][] = (float) $record->humidity_internal;
            $buckets[$bucketKey]['temp_external'][] = (float) $record->temp_external;
            $buckets[$bucketKey]['humidity_external'][] = (float) $record->humidity_external;
            $buckets[$bucketKey]['solar_radiation'][] = (float) $record->solar_radiation;
            $buckets[$bucketKey]['grain_moisture'][] = (float) $record->grain_moisture;
            $buckets[$bucketKey]['weight_kg'][] = (float) $record->weight_kg;
            $buckets[$bucketKey]['heater_status'] = $buckets[$bucketKey]['heater_status'] || (bool) $record->heater_status;
            $buckets[$bucketKey]['heater_level'][] = (int) $record->heater_level;
            $buckets[$bucketKey]['exhaust_fan_status'] = $buckets[$bucketKey]['exhaust_fan_status'] || (bool) $record->exhaust_fan_status;
            $buckets[$bucketKey]['exhaust_fan_speed'][] = (int) $record->exhaust_fan_speed;
            $buckets[$bucketKey]['count']++;
        }

        // If bucket count is virtually identical to raw count, skip
        if (count($buckets) >= $totalRawRows && !$force) {
            return [
                'batchId' => $batchId,
                'batchCode' => $batch->batch_code,
                'status' => 'SKIPPED',
                'reason' => 'Data sudah terkompresi / teragregasi sebelumnya.',
                'originalRows' => $totalRawRows,
                'downsampledRows' => count($buckets),
                'savedRows' => 0,
                'reductionPercentage' => 0.0,
            ];
        }

        // Prepare aggregated downsampled dataset
        $aggregatedPayloads = [];
        $now = Carbon::now();

        foreach ($buckets as $bucketTimestamp => $data) {
            $avg = fn(array $arr) => !empty($arr) ? round(array_sum($arr) / count($arr), 2) : 0.0;

            $aggregatedPayloads[] = [
                'batch_id' => $batchId,
                'temp_internal' => $avg($data['temp_internal']),
                'humidity_internal' => $avg($data['humidity_internal']),
                'temp_external' => $avg($data['temp_external']),
                'humidity_external' => $avg($data['humidity_external']),
                'solar_radiation' => $avg($data['solar_radiation']),
                'grain_moisture' => $avg($data['grain_moisture']),
                'weight_kg' => $avg($data['weight_kg']),
                'heater_status' => $data['heater_status'] ? 1 : 0,
                'heater_level' => (int) round($avg($data['heater_level'])),
                'exhaust_fan_status' => $data['exhaust_fan_status'] ? 1 : 0,
                'exhaust_fan_speed' => (int) round($avg($data['exhaust_fan_speed'])),
                'recorded_at' => Carbon::createFromTimestamp($bucketTimestamp)->toDateTimeString(),
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ];
        }

        // Execute in Atomic Database Transaction
        DB::transaction(function () use ($batchId, $aggregatedPayloads) {
            // Delete old high-frequency raw telemetry records for this batch
            Telemetry::where('batch_id', $batchId)->delete();

            // Insert newly aggregated records in chunks of 500
            foreach (array_chunk($aggregatedPayloads, 500) as $chunk) {
                Telemetry::insert($chunk);
            }
        });

        $downsampledRows = count($aggregatedPayloads);
        $savedRows = $totalRawRows - $downsampledRows;
        $reductionPercentage = $totalRawRows > 0 ? round(($savedRows / $totalRawRows) * 100, 2) : 0.0;

        Log::info("Telemetry downsampled for Batch #{$batch->batch_code}: {$totalRawRows} raw rows -> {$downsampledRows} aggregated rows (Saved {$savedRows} rows, -{$reductionPercentage}%)");

        return [
            'batchId' => $batchId,
            'batchCode' => $batch->batch_code,
            'status' => 'SUCCESS',
            'intervalMinutes' => $intervalMinutes,
            'originalRows' => $totalRawRows,
            'downsampledRows' => $downsampledRows,
            'savedRows' => $savedRows,
            'reductionPercentage' => $reductionPercentage,
        ];
    }

    /**
     * Downsample all completed or aborted batches that have high raw row counts.
     *
     * @param int $intervalMinutes Bucket interval (default 5 minutes)
     * @param bool $force Force downsampling
     * @return array Aggregated summary
     */
    public function downsampleAllCompletedBatches(int $intervalMinutes = 5, bool $force = false): array
    {
        $batches = Batch::whereIn('status', ['COMPLETED', 'ABORTED'])->orderBy('created_at', 'asc')->get();

        $results = [];
        $totalOriginal = 0;
        $totalDownsampled = 0;
        $totalSaved = 0;

        foreach ($batches as $batch) {
            $res = $this->downsampleBatch($batch, $intervalMinutes, $force);
            $results[] = $res;

            $totalOriginal += $res['originalRows'] ?? 0;
            $totalDownsampled += $res['downsampledRows'] ?? 0;
            $totalSaved += $res['savedRows'] ?? 0;
        }

        $overallReduction = $totalOriginal > 0 ? round(($totalSaved / $totalOriginal) * 100, 2) : 0.0;

        return [
            'totalBatchesProcessed' => count($batches),
            'intervalMinutes' => $intervalMinutes,
            'totalOriginalRows' => $totalOriginal,
            'totalDownsampledRows' => $totalDownsampled,
            'totalSavedRows' => $totalSaved,
            'overallReductionPercentage' => $overallReduction,
            'details' => $results,
        ];
    }

    /**
     * Get database telemetry storage metrics.
     */
    public function getStorageStats(): array
    {
        $totalTelemetries = Telemetry::count();
        $totalBatches = Batch::count();
        $completedBatches = Batch::whereIn('status', ['COMPLETED', 'ABORTED'])->count();
        $activeBatches = Batch::where('status', 'ACTIVE')->count();

        // Estimate row size ~ 150 bytes per row + indexes ~ 70 bytes = ~220 bytes
        $estimatedSizeBytes = $totalTelemetries * 220;
        $estimatedSizeMb = round($estimatedSizeBytes / (1024 * 1024), 2);

        $oldestReading = Telemetry::min('recorded_at');
        $newestReading = Telemetry::max('recorded_at');

        return [
            'totalTelemetryRows' => $totalTelemetries,
            'estimatedDatabaseSizeMb' => $estimatedSizeMb,
            'totalBatches' => $totalBatches,
            'completedBatches' => $completedBatches,
            'activeBatches' => $activeBatches,
            'oldestReading' => $oldestReading,
            'newestReading' => $newestReading,
            'recommendedAction' => $totalTelemetries > 20000 ? 'Disarankan menjalankan downsampling untuk mengoptimalkan ruang database.' : 'Ukuran database dalam kondisi optimal.',
        ];
    }
}
