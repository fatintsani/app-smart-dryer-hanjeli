@extends('emails.layouts.master')

@section('title', 'Laporan Hasil Pengeringan Hanjeli - ' . $batch->batch_code)
@section('header_badge', 'SESI PENGERINGAN SELESAI')
@section('header_title', 'Sertifikat & Ringkasan Panen')
@section('header_subtitle', 'Batch #' . $batch->batch_code . ' • ' . $batch->crop_variety)

@section('content')
    <div class="email-greeting">Halo, {{ $operatorName ?? 'Operator Greenhouse' }}!</div>
    
    <p class="email-paragraph">
        Sesi pengeringan hanjeli untuk batch <strong>#{{ $batch->batch_code }}</strong> telah selesai diproses dengan sukses. Seluruh data siklus suhu, kelembapan, dan kualitas akhir telah diarsipkan ke dalam database sistem.
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
                <td class="metric-value" style="color: #0D631B; font-size: 15px;">
                    <strong>{{ $batch->final_moisture_percent ?? $batch->current_moisture_percent }}%</strong> 
                    <span style="font-size: 11.5px; color: #64748B;">(Awal: {{ $batch->initial_moisture_percent }}%)</span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Berat Bersih Akhir</td>
                <td class="metric-value">
                    <strong>{{ $batch->final_weight_kg ?? $batch->current_weight_kg }} kg</strong> 
                    <span style="font-size: 11.5px; color: #64748B;">(Awal: {{ $batch->initial_weight_kg }} kg)</span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Total Durasi Proses</td>
                <td class="metric-value">{{ $batch->total_duration_hours }} Jam</td>
            </tr>
            <tr>
                <td class="metric-label">Konsumsi Energi Listrik</td>
                <td class="metric-value">{{ $batch->energy_kwh }} kWh</td>
            </tr>
            <tr>
                <td class="metric-label">Mutu / Grade Kualitas</td>
                <td class="metric-value" style="color: #15803D; font-weight: 800;">
                    {{ $batch->quality_grade ?? 'Grade A (Ekspor)' }}
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/history') }}" class="btn-action">
        Lihat Riwayat & Unduh Sertifikat →
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-info">
        <strong>Penyimpanan Pascapanen:</strong> Biji hanjeli telah mencapai standar kadar air simpan aman (&le; 12.0%). Disarankan segera dikemas dalam karung kedap udara untuk menjaga nutrisi dan tekstur gabah.
    </div>
@endsection
