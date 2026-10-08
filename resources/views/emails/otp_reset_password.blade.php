@extends('emails.layouts.master')

@section('title', 'Kode OTP Pemulihan Kata Sandi - Smart Dryer Hanjeli')
@section('header_badge', 'KEAMANAN AKUN')
@section('header_title', 'Pemulihan Kata Sandi')
@section('header_subtitle', 'Verifikasi identitas akun Smart Room Dryer Hanjeli')

@section('content')
    <div class="email-greeting">Halo, {{ $name }}!</div>
    
    <p class="email-paragraph">
        Kami menerima permintaan untuk mereset kata sandi akun sistem <strong>Smart Dryer Hanjeli</strong> Anda. Silakan gunakan 6-digit kode OTP di bawah ini untuk mengonfirmasi permintaan Anda:
    </p>

    <!-- OTP Code Display -->
    <div class="otp-wrapper">
        <span class="otp-tag">KODE OTP VERIFIKASI</span>
        <div class="otp-number">{{ $otpCode }}</div>
        <div class="otp-timer">Kode ini berlaku selama <strong>{{ $expiryMinutes }} menit</strong> ke depan.</div>
    </div>

    <p class="email-paragraph" style="font-size: 13.5px; color: #64748B; margin-top: 10px;">
        Masukkan kode ini pada formulir verifikasi di aplikasi untuk membuat kata sandi baru Anda.
    </p>
@endsection

@section('notice')
    <div class="alert-box alert-box-warning">
        <strong>Pemberitahuan Keamanan:</strong> Jangan berikan kode OTP ini kepada siapa pun, termasuk pihak administrator atau tim teknis. Jika Anda tidak pernah meminta perubahan kata sandi, abaikan email ini dan akun Anda tetap terlindungi.
    </div>
@endsection
