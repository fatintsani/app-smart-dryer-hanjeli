@extends('emails.layouts.master')

@section('title', 'Selamat Datang di Smart Room Dryer Hanjeli')
@section('header_badge', 'AKUN PENGGUNA BARU')
@section('header_title', 'Selamat Datang di Sistem Smart Dryer')
@section('header_subtitle', 'Greenhouse Pengeringan Hanjeli Waluran & CoE STAS-RG')

@section('content')
    <div class="email-greeting">Halo, {{ $name }}!</div>
    
    <p class="email-paragraph">
        Akun Anda telah berhasil didaftarkan ke dalam platform <strong>Smart Room Dryer Hanjeli</strong>. Anda sekarang memiliki akses untuk memantau sensor secara real-time, mengontrol siklus pengeringan, dan mengelola riwayat panen.
    </p>

    <!-- User Details Metric Card -->
    <div class="metric-card">
        <table class="metric-table" role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="metric-label">Nama Pengguna</td>
                <td class="metric-value">{{ $name }}</td>
            </tr>
            <tr>
                <td class="metric-label">Alamat Email</td>
                <td class="metric-value" style="color: #0D631B;">{{ $email }}</td>
            </tr>
            <tr>
                <td class="metric-label">Hak Akses / Peran</td>
                <td class="metric-value" style="font-weight: 800; color: #166534;">
                    {{ $role ?? 'OPERATOR' }}
                </td>
            </tr>
            <tr>
                <td class="metric-label">Lokasi Fasilitas</td>
                <td class="metric-value">Desa Wisata Hanjeli, Waluran, Sukabumi</td>
            </tr>
            <tr>
                <td class="metric-label">Waktu Pendaftaran</td>
                <td class="metric-value">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
@endsection

@section('action')
    <a href="{{ url('/login') }}" class="btn-action">
        Masuk ke Dashboard Sistem →
    </a>
@endsection

@section('notice')
    <div class="alert-box alert-box-info">
        <strong>Tips Keamanan & Passkey:</strong> Setelah login ke dashboard, Anda dapat mengaktifkan fitur <em>Passkey / Biometrik (Sidik Jari / Windows Hello)</em> di menu Pengaturan Akun untuk login instan tanpa kata sandi.
    </div>
@endsection
