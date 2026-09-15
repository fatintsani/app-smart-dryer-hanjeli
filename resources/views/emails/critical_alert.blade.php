@extends('emails.layouts.master')

@section('title', 'Peringatan Sensor Kritis - Smart Dryer Hanjeli')
@section('header_badge', 'ALERT SENSOR SISTEM')
@section('header_title', $alertTitle ?? 'Peringatan Operasional Sensor IoT')
@section('header_subtitle', 'Terdeteksi kondisi di luar batas parameter standar')

@section('content')
    <div class="email-greeting">Perhatian Operator & Admin Greenhouse,</div>
    
    <p class="email-paragraph">
        Sistem pemantauan cerdas mendeteksi adanya anomali atau kondisi kritis pada perangkat ruang pengeringan. Harap segera lakukan pemeriksaan operasional:
    </p>

    <!-- Danger Alert Box -->
    <div class="alert-box alert-box-danger">
        <strong>Pesan Peringatan:</strong> {{ $alertMessage }}
    </div>

    <!-- Parameter Telemetry Details -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Kategori Peringatan</td>
                <td class="metric-value">{{ $category ?? 'SENSOR' }}</td>
            </tr>
            <tr>
                <td class="metric-label">Tingkat Bahaya</td>
                <td class="metric-value" style="color: #DC2626;">{{ $level ?? 'CRITICAL' }}</td>
            </tr>
            @if(isset($tempInternal))
            <tr>
                <td class="metric-label">Suhu Ruang Terukur</td>
                <td class="metric-value">{{ $tempInternal }} °C</td>
            </tr>
            @endif
            @if(isset($humidityInternal))
            <tr>
                <td class="metric-label">Kelembapan Terukur</td>
                <td class="metric-value">{{ $humidityInternal }} % RH</td>
            </tr>
            @endif
            <tr>
                <td class="metric-label">Waktu Pencatatan</td>
                <td class="metric-value">{{ $recordedTime ?? now()->translatedFormat('d F Y, H:i') . ' WIB' }}</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/monitoring') }}" class="btn-action">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:6px;">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
        </svg>
        Buka Layar Monitoring Real-time
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-warning">
        <strong>Tindakan yang Disarankan:</strong> Periksa putaran kipas sirkulasi dan relai pemanas bantu di panel kontrol greenhouse untuk mencegah risiko degradasi mutu biji hanjeli.
    </div>
@endsection
