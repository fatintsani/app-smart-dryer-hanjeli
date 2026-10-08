@extends('emails.layouts.master')

@section('title', 'Selamat Datang di Smart Room Dryer Hanjeli')
@section('header_badge', 'AKUN PENGGUNA BARU')
@section('header_title', 'Selamat Datang di Sistem Smart Dryer')
@section('header_subtitle', 'Greenhouse Pengeringan Hanjeli Waluran & CoE STAS-RG Telkom University')

@section('content')
    <div class="email-greeting">Halo, {{ $name }}!</div>
    
    <p class="email-paragraph">
        Akun Anda telah berhasil terdaftar pada platform <strong>Smart Room Dryer Hanjeli</strong>. Anda kini dapat memantau telemetri sensor lingkungan, memonitor status siklus pengeringan, dan mengakses data ketertelusuran (<em>traceability</em>) secara terpusat.
    </p>

    <!-- User Details Metric Card -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Nama Lengkap</td>
                <td class="metric-value">{{ $name }}</td>
            </tr>
            @if(isset($username) && !empty($username))
            <tr>
                <td class="metric-label">Username</td>
                <td class="metric-value" style="color: #0D631B; font-weight: 700;">
                    <span>@</span>{{ $username }}
                </td>
            </tr>
            @endif
            <tr>
                <td class="metric-label">Alamat Email</td>
                <td class="metric-value" style="color: #0D631B;">{{ $email }}</td>
            </tr>
            <tr>
                <td class="metric-label">Hak Akses / Peran</td>
                <td class="metric-value">
                    <span style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; background: #EAF8EB; color: #0D631B;">
                        {{ $role ?? 'OPERATOR' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="metric-label">Lokasi Greenhouse</td>
                <td class="metric-value" style="font-size: 12.5px;">Desa Wisata Hanjeli, Waluran Mandiri, Sukabumi</td>
            </tr>
            <tr>
                <td class="metric-label">Waktu Pendaftaran</td>
                <td class="metric-value" style="font-size: 12.5px;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/login') }}" class="btn-action">
        Masuk ke Dashboard Sistem
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-info">
        <strong>Tips Keamanan &amp; Akses:</strong> Anda dapat menggunakan <strong>Username</strong> atau <strong>Email</strong> bersama kata sandi untuk masuk ke aplikasi. Anda juga dapat mengaktifkan fitur <em>Passkey / Biometrik (Sidik Jari / Windows Hello)</em> di menu profil untuk kemudahan login berikutnya.
    </div>
@endsection
