@extends('emails.layouts.master')

@section('title', 'Peringatan Sensor Kritis - Smart Dryer Hanjeli')
@section('header_badge', 'ALERT SENSOR IOT')
@section('header_title', $alertTitle ?? 'Peringatan Operasional Ruang Pengering')
@section('header_subtitle', 'Terdeteksi kondisi lingkungan di luar batas parameter aman')

@section('content')
    <div class="email-greeting">Perhatian Tim Operator &amp; Administrator,</div>
    
    <p class="email-paragraph">
        Sistem monitoring IoT <strong>Smart Room Dryer Hanjeli</strong> mendeteksi adanya pembacaan parameter di luar ambang batas standar operasional pengeringan:
    </p>

    <!-- Danger Alert Box -->
    <div class="alert-box alert-box-danger">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="vertical-align: top;">
                    <strong style="font-size: 14px; display: block; margin-bottom: 3px; color: #991B1B;">⚠️ Pesan Peringatan Kritis:</strong>
                    <div style="font-size: 13.5px; line-height: 1.55; color: #7F1D1D;">
                        {{ $alertMessage }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Telemetry Parameter Details -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Kategori Alert</td>
                <td class="metric-value">
                    <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #FEE2E2; color: #991B1B;">
                        {{ $category ?? 'SENSOR' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Tingkat Bahaya</td>
                <td class="metric-value" style="color: #DC2626; font-weight: 800;">{{ $level ?? 'CRITICAL' }}</td>
            </tr>
            @if(isset($tempInternal))
            <tr>
                <td class="metric-label">Suhu Internal Ruang</td>
                <td class="metric-value" style="color: #DC2626;">{{ $tempInternal }} °C</td>
            </tr>
            @endif
            @if(isset($humidityInternal))
            <tr>
                <td class="metric-label">Kelembapan Udara (RH)</td>
                <td class="metric-value">{{ $humidityInternal }} % RH</td>
            </tr>
            @endif
            <tr>
                <td class="metric-label">Waktu Deteksi</td>
                <td class="metric-value" style="font-size: 12.5px;">{{ $recordedTime ?? now()->translatedFormat('d F Y, H:i:s') . ' WIB' }}</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/monitoring') }}" class="btn-action">
        Buka Layar Monitoring Real-time
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-warning">
        <strong>Langkah Penanganan Cepat:</strong> Periksa sirkulasi blower ventilasi, sensor DHT22, dan elemen pemanas bantuan pada panel kontrol smart dryer untuk menjaga stabilitas kualitas gabah hanjeli.
    </div>
@endsection
