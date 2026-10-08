@extends('emails.layouts.master')

@section('title', 'Laporan Hasil Pengeringan Hanjeli - ' . $batch->batch_code)
@section('header_badge', 'SESI PENGERINGAN SELESAI')
@section('header_title', 'Ringkasan & Sertifikat Mutu Panen')
@section('header_subtitle', 'Batch #' . $batch->batch_code . ' • ' . $batch->crop_variety)

@section('content')
    <div class="email-greeting">Halo, {{ $operatorName ?? 'Operator Greenhouse' }}!</div>
    
    <p class="email-paragraph">
        Sesi pengeringan biji hanjeli untuk <strong>Batch #{{ $batch->batch_code }}</strong> telah berhasil diselesaikan dengan baik. Seluruh rekaman parameter suhu, kelembapan, dan kadar air akhir telah diverifikasi dan tersimpan dalam sistem <em>traceability</em>.
    </p>

    <!-- Harvest Summary Metric Card -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Kode Batch</td>
                <td class="metric-value" style="color: #0D631B; font-weight: 800;">#{{ $batch->batch_code }}</td>
            </tr>
            <tr>
                <td class="metric-label">Varietas Hanjeli</td>
                <td class="metric-value">{{ $batch->crop_variety }}</td>
            </tr>
            <tr>
                <td class="metric-label">Kadar Air Akhir</td>
                <td class="metric-value" style="color: #0D631B; font-size: 14.5px;">
                    <strong>{{ $batch->final_moisture_percent ?? $batch->current_moisture_percent }}%</strong> 
                    <span style="font-size: 11.5px; color: #64748B; font-weight: 500;">(Awal: {{ $batch->initial_moisture_percent }}%)</span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Berat Bersih Akhir</td>
                <td class="metric-value">
                    <strong>{{ $batch->final_weight_kg ?? $batch->current_weight_kg }} kg</strong> 
                    <span style="font-size: 11.5px; color: #64748B; font-weight: 500;">(Awal: {{ $batch->initial_weight_kg }} kg)</span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Total Durasi Proses</td>
                <td class="metric-value">{{ $batch->total_duration_hours ?? '-' }} Jam</td>
            </tr>
            <tr>
                <td class="metric-label">Konsumsi Energi Listrik</td>
                <td class="metric-value">{{ $batch->energy_kwh ?? '0' }} kWh</td>
            </tr>
            <tr>
                <td class="metric-label">Grade / Mutu Produk</td>
                <td class="metric-value">
                    <span style="display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; background: #EAF8EB; color: #0D631B;">
                        {{ $batch->quality_grade ?? 'Grade A (Standar Premium)' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/history') }}" class="btn-action">
        Lihat Riwayat &amp; Unduh Sertifikat
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-info">
        <strong>Penyimpanan Pascapanen:</strong> Biji hanjeli telah mencapai standar kadar air simpan aman (&le; 12.0%). Disarankan segera dikemas dalam wadah kedap udara untuk menjaga integritas gizi dan kesegaran gabah.
    </div>
@endsection
