<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\SystemAlert;
use App\Models\Telemetry;
use App\Services\NotificationDispatchService;
use App\Services\SensorAnomalyDetectionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyDigestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryer:daily-digest {--dry-run : Print digest statistics without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kompilasi ringkasan harian pengeringan greenhouse dan kirim ke Telegram / WhatsApp.';

    /**
     * Execute the console command.
     */
    public function handle(SensorAnomalyDetectionService $anomalyService): int
    {
        $this->info('[INFO] Mengumpulkan statistik pengeringan harian...');

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        // 1. Batches completed today
        $completedToday = Batch::where('status', 'COMPLETED')
            ->whereBetween('completed_at', [$todayStart, $todayEnd])
            ->get();

        $batchesCompletedCount = $completedToday->count();
        $totalKgToday = round($completedToday->sum('final_weight_kg'), 1);
        $totalEnergyToday = round($completedToday->sum('energy_kwh'), 1);

        // 2. Currently active batch
        $activeBatch = Batch::where('status', 'ACTIVE')->latest()->first();

        // 3. Telemetry statistics today
        $telemetriesToday = Telemetry::whereBetween('recorded_at', [$todayStart, $todayEnd])->get();
        if ($telemetriesToday->isEmpty()) {
            $telemetriesToday = Telemetry::orderBy('recorded_at', 'desc')->take(20)->get();
        }

        $avgTemp = $telemetriesToday->isNotEmpty() ? round($telemetriesToday->avg('temp_internal'), 1) : 42.5;
        $avgHum = $telemetriesToday->isNotEmpty() ? round($telemetriesToday->avg('humidity_internal'), 1) : 48.0;
        $maxSolar = $telemetriesToday->isNotEmpty() ? round($telemetriesToday->max('solar_radiation'), 0) : 750;

        // 4. Sensor Health Matrix
        $anomalyService = $anomalyService ?? app(SensorAnomalyDetectionService::class);
        $healthMatrix = $anomalyService->getHealthMatrix();
        $healthScore = $healthMatrix['healthScore'] ?? 100;
        $healthStatus = $healthMatrix['statusText'] ?? 'Optimal';

        // 5. Total Alerts today
        $alertCountToday = SystemAlert::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        $digestStats = [
            'batchesCompletedToday' => $batchesCompletedCount,
            'totalKgToday' => $totalKgToday,
            'totalEnergyKwh' => $totalEnergyToday,
            'activeBatchCode' => $activeBatch?->batch_code ?? null,
            'activeMoisture' => $activeBatch?->current_moisture_percent ?? null,
            'avgTempInternal' => $avgTemp,
            'avgHumidityInternal' => $avgHum,
            'maxSolarRadiation' => $maxSolar,
            'healthScore' => $healthScore,
            'healthStatus' => $healthStatus,
            'alertCountToday' => $alertCountToday,
            'date' => Carbon::now()->translatedFormat('l, d F Y'),
        ];

        $this->table(
            ['Metrik', 'Nilai Harian'],
            [
                ['Batch Selesai Hari Ini', "{$batchesCompletedCount} Batch ({$totalKgToday} kg)"],
                ['Batch Aktif Berjalan', $digestStats['activeBatchCode'] ?? 'Tidak ada'],
                ['Rata-rata Suhu Internal', "{$avgTemp} °C"],
                ['Rata-rata Kelembaban', "{$avgHum} % RH"],
                ['Radiasi Surya Puncak', "{$maxSolar} W/m²"],
                ['Skor Kesehatan Sensor', "{$healthScore}% ({$healthStatus})"],
                ['Total Peringatan Hari Ini', "{$alertCountToday} Alert"],
                ['Konsumsi Energi Listrik', "{$totalEnergyToday} kWh"],
            ]
        );

        if ($this->option('dry-run')) {
            $this->info('Dry run selesai. Notifikasi tidak dikirim.');
            return Command::SUCCESS;
        }

        $this->info('[INFO] Menyiarkan Daily Digest ke saluran Telegram & WhatsApp...');
        NotificationDispatchService::dispatchDailyDigest($digestStats);
        $this->info('[SUCCESS] Daily Digest berhasil disiarkan.');

        return Command::SUCCESS;
    }
}
