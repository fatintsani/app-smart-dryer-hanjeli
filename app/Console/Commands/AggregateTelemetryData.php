<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Services\TelemetryAggregationService;
use Illuminate\Console\Command;

class AggregateTelemetryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'greenhouse:downsample-telemetry
                            {--batch= : ID atau Kode Batch spesifik yang ingin di-downsample}
                            {--interval=5 : Interval resolusi bucket dalam menit (contoh: 5, 15, 60)}
                            {--force : Paksa downsampling meskipun baris data sedikit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agregasi / Downsample data telemetri historis (raw 5-detik menjadi rata-rata 5-menit/1-jam) untuk efisiensi ukuran database dan kecepatan kueri riwayat.';

    /**
     * Execute the console command.
     */
    public function handle(TelemetryAggregationService $service): int
    {
        $this->info('================================================================');
        $this->info('  SMART DRYER HANJELI — TELEMETRY DOWNSAMPLING WORKER');
        $this->info('================================================================');

        $intervalMinutes = max(1, (int) $this->option('interval'));
        $force = (bool) $this->option('force');
        $batchOption = $this->option('batch');

        $this->comment("Resolusi Agregasi : Setiap {$intervalMinutes} Menit");

        if ($batchOption) {
            $batch = Batch::where('id', $batchOption)
                ->orWhere('batch_code', $batchOption)
                ->first();

            if (!$batch) {
                $this->error("Batch '{$batchOption}' tidak ditemukan.");
                return Command::FAILURE;
            }

            $this->info("Memproses Batch Spesifik: #{$batch->batch_code} ({$batch->crop_variety})...");
            $res = $service->downsampleBatch($batch, $intervalMinutes, $force);

            $this->table(
                ['Batch Code', 'Status', 'Raw Rows', 'Downsampled Rows', 'Saved Rows', 'Efficiency'],
                [[
                    $res['batchCode'],
                    $res['status'],
                    number_format($res['originalRows']),
                    number_format($res['downsampledRows']),
                    number_format($res['savedRows']),
                    $res['reductionPercentage'] . '%'
                ]]
            );

            if ($res['status'] === 'SKIPPED') {
                $this->warn("Catatan: {$res['reason']}");
            } else {
                $this->info("Sukses mengompresi batch #{$batch->batch_code}!");
            }

            return Command::SUCCESS;
        }

        // Process all completed / aborted batches
        $this->info('Menganalisis seluruh batch yang telah selesai...');
        $statsBefore = $service->getStorageStats();
        $this->line("Total Baris Database Saat Ini : " . number_format($statsBefore['totalTelemetryRows']) . " baris (~{$statsBefore['estimatedDatabaseSizeMb']} MB)");

        $summary = $service->downsampleAllCompletedBatches($intervalMinutes, $force);

        if (empty($summary['details'])) {
            $this->warn('Tidak ada batch yang memenuhi kriteria untuk di-downsample.');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($summary['details'] as $d) {
            $rows[] = [
                $d['batchCode'],
                $d['status'],
                number_format($d['originalRows']),
                number_format($d['downsampledRows']),
                number_format($d['savedRows']),
                $d['reductionPercentage'] . '%'
            ];
        }

        $this->table(
            ['Batch Code', 'Status', 'Raw Rows', 'Downsampled Rows', 'Saved Rows', 'Efficiency'],
            $rows
        );

        $this->info("----------------------------------------------------------------");
        $this->info("Total Batch Diproses       : {$summary['totalBatchesProcessed']} batch");
        $this->info("Total Baris Awal (Raw)      : " . number_format($summary['totalOriginalRows']) . " baris");
        $this->info("Total Baris Setelah Agregasi: " . number_format($summary['totalDownsampledRows']) . " baris");
        $this->info("Total Baris Dihemat         : " . number_format($summary['totalSavedRows']) . " baris");
        $this->info("Persentase Penghematan Total: {$summary['overallReductionPercentage']}%");
        $this->info("================================================================\n");

        return Command::SUCCESS;
    }
}
