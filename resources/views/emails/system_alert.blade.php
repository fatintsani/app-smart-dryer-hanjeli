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
        default => 'alert-box-info',
    };
@endphp

@section('title', $alertTitle . ' - Smart Dryer Hanjeli')
@section('header_badge', $badgeText)
@section('header_title', $alertTitle ?? 'Notifikasi Sistem Pengeringan')
@section('header_subtitle', 'Pemberitahuan Otomatis IoT Smart Greenhouse Hanjeli')

@section('content')
    @php
        $badgeColor = match($levelUpper) {
            'CRITICAL', 'EMERGENCY' => '#DC2626',
            'WARNING' => '#D97706',
            'SUCCESS' => '#059669',
            default => '#0D631B',
        };
    @endphp
    <div class="email-greeting">Halo Tim Operator &amp; Administrator Greenhouse,</div>
    
    <p class="email-paragraph">
        Sistem pemantauan cerdas Smart Dryer Hanjeli mencatat aktivitas baru yang memerlukan perhatian operasional Anda:
    </p>

    <!-- Dynamic Alert Box with Inline SVG Icon -->
    <div class="alert-box {{ $alertBoxClass }}">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="width: 24px; vertical-align: top; padding-right: 10px;">
                    @if($levelUpper === 'CRITICAL' || $levelUpper === 'EMERGENCY')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    @elseif($levelUpper === 'WARNING')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    @elseif($levelUpper === 'SUCCESS')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    @endif
                </td>
                <td style="vertical-align: top;">
                    <strong style="font-size: 13.5px; display: block; margin-bottom: 2px;">Pesan Notifikasi:</strong>
                    <div style="font-size: 13px; line-height: 1.55;">
                        {{ $alertMessage }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Parameter / Metadata Details Card with SVG Icons -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Kategori Notifikasi
                </td>
                <td class="metric-value">
                    <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; background: #E2E8F0; color: #334155;">
                        {{ $category ?? 'SYSTEM' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    Tingkat Prioritas
                </td>
                <td class="metric-value">
                    <strong style="color: {{ $badgeColor }};">{{ $levelUpper }}</strong>
                </td>
            </tr>
            @if(!empty($batchCode))
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                    Kode Batch
                </td>
                <td class="metric-value" style="color: #0D631B; font-family: monospace; font-size: 13.5px;">
                    #{{ $batchCode }}
                </td>
            </tr>
            @endif
            @if(isset($tempInternal) && $tempInternal !== null)
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Suhu Ruang Pengering
                </td>
                <td class="metric-value">{{ number_format($tempInternal, 1) }} °C</td>
            </tr>
            @endif
            @if(isset($humidityInternal) && $humidityInternal !== null)
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                    </svg>
                    Kelembapan Ruang (RH)
                </td>
                <td class="metric-value">{{ number_format($humidityInternal, 1) }} % RH</td>
            </tr>
            @endif
            @if(isset($grainMoisture) && $grainMoisture !== null)
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <path d="M2 12h20M12 2v20"></path>
                    </svg>
                    Kadar Air Gabah (MC)
                </td>
                <td class="metric-value" style="color: #0284C7;">{{ number_format($grainMoisture, 1) }} %</td>
            </tr>
            @endif
            <tr>
                <td class="metric-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right:5px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Waktu Pencatatan
                </td>
                <td class="metric-value">{{ $recordedTime ?? now()->translatedFormat('d F Y, H:i') . ' WIB' }}</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ $actionUrl ?? url('/monitoring') }}" class="btn-action">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:6px;">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
        </svg>
        Buka Dashboard Sistem Smart Dryer
    </a>
@endsection

@section('notice')
    @if($levelUpper === 'CRITICAL' || $levelUpper === 'EMERGENCY')
        <div class="alert-box alert-box-warning">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="width: 22px; vertical-align: top; padding-right: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="display:block;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </td>
                    <td style="vertical-align: top;">
                        <strong>Tindakan Cepat yang Disarankan:</strong> Segera lakukan inspeksi fisik atau periksa kecepatan kipas exhaust dan relai pemanas bantu di dashboard untuk mencegah risiko pembusukan atau overheating gabah hanjeli.
                    </td>
                </tr>
            </table>
        </div>
    @elseif($category === 'BATCH')
        <div class="alert-box alert-box-info">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="width: 22px; vertical-align: top; padding-right: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D631B" stroke-width="2" style="display:block;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </td>
                    <td style="vertical-align: top;">
                        <strong>Catatan Operasional:</strong> Pastikan data fisik berat gabah dan kelembapan berkala telah dicatat ke dalam logbook digital pada aplikasi.
                    </td>
                </tr>
            </table>
        </div>
    @endif
@endsection
