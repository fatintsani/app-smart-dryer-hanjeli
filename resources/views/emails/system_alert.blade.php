@extends('emails.layouts.master')

@php
    $levelUpper = strtoupper($level ?? 'INFO');
    $badgeText = match($levelUpper) {
        'CRITICAL', 'EMERGENCY' => 'PERINGATAN KRITIS SISTEM',
        'WARNING' => 'PERINGATAN OPERASIONAL',
        'SUCCESS' => 'PEMBERITAHUAN SUKSES',
        default => 'NOTIFIKASI SISTEM',
    };

    $badgeColor = match($levelUpper) {
        'CRITICAL', 'EMERGENCY' => '#DC2626',
        'WARNING' => '#D97706',
        'SUCCESS' => '#059669',
        default => '#0D631B',
    };

    $alertBoxClass = match($levelUpper) {
        'CRITICAL', 'EMERGENCY' => 'alert-box-danger',
        'WARNING' => 'alert-box-warning',
        'SUCCESS' => 'alert-box-info',
        default => 'alert-box-info',
    };
@endphp

@section('title', ($alertTitle ?? 'Notifikasi Sistem') . ' - Smart Dryer Hanjeli')
@section('header_badge', $badgeText)
@section('header_title', $alertTitle ?? 'Notifikasi Sistem Pengeringan')
@section('header_subtitle', 'Pemberitahuan Otomatis IoT Smart Greenhouse Hanjeli')

@section('content')
    <div class="email-greeting">Halo Tim Operator &amp; Administrator Greenhouse,</div>
    
    <p class="email-paragraph">
        Sistem monitoring cerdas <strong>Smart Dryer Hanjeli</strong> mencatat aktivitas baru yang memerlukan perhatian operasional Anda:
    </p>

    <!-- Dynamic Alert Box -->
    <div class="alert-box {{ $alertBoxClass }}">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="vertical-align: top;">
                    <strong style="font-size: 13.5px; display: block; margin-bottom: 3px;">Pesan Notifikasi:</strong>
                    <div style="font-size: 13px; line-height: 1.55;">
                        {{ $alertMessage }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Parameter / Metadata Details Card -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Kategori Notifikasi</td>
                <td class="metric-value">
                    <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #E2E8F0; color: #334155;">
                        {{ $category ?? 'SYSTEM' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Tingkat Prioritas</td>
                <td class="metric-value">
                    <strong style="color: {{ $badgeColor }};">{{ $levelUpper }}</strong>
                </td>
            </tr>
            @if(!empty($batchCode))
            <tr>
                <td class="metric-label">Kode Batch</td>
                <td class="metric-value" style="color: #0D631B; font-family: monospace; font-size: 13.5px;">
                    #{{ $batchCode }}
                </td>
            </tr>
            @endif
            @if(isset($tempInternal) && $tempInternal !== null)
            <tr>
                <td class="metric-label">Suhu Ruang Pengering</td>
                <td class="metric-value">{{ number_format($tempInternal, 1) }} °C</td>
            </tr>
            @endif
            @if(isset($humidityInternal) && $humidityInternal !== null)
            <tr>
                <td class="metric-label">Kelembapan Ruang (RH)</td>
                <td class="metric-value">{{ number_format($humidityInternal, 1) }} % RH</td>
            </tr>
            @endif
            @if(isset($grainMoisture) && $grainMoisture !== null)
            <tr>
                <td class="metric-label">Kadar Air Gabah (MC)</td>
                <td class="metric-value" style="color: #0284C7;">{{ number_format($grainMoisture, 1) }} %</td>
            </tr>
            @endif
            <tr>
                <td class="metric-label">Waktu Pencatatan</td>
                <td class="metric-value" style="font-size: 12.5px;">{{ $recordedTime ?? now()->translatedFormat('d F Y, H:i') . ' WIB' }}</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ $actionUrl ?? url('/monitoring') }}" class="btn-action">
        Buka Dashboard Sistem
    </a>
@endsection

@section('notice')
    @if($levelUpper === 'CRITICAL' || $levelUpper === 'EMERGENCY')
        <div class="alert-box alert-box-warning">
            <strong>Tindakan Cepat yang Disarankan:</strong> Segera lakukan inspeksi fisik atau periksa kecepatan kipas exhaust dan relai pemanas bantu di dashboard untuk mencegah risiko pembusukan atau overheating gabah hanjeli.
        </div>
    @elseif(($category ?? '') === 'BATCH')
        <div class="alert-box alert-box-info">
            <strong>Catatan Operasional:</strong> Pastikan data fisik berat gabah dan kelembapan berkala telah dicatat ke dalam logbook digital pada aplikasi.
        </div>
    @endif
@endsection
