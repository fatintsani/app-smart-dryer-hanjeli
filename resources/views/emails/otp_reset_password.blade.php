@extends('emails.layouts.master')

@section('title', 'Kode Keamanan Pemulihan Kata Sandi - Smart Dryer Hanjeli')
@section('header_badge', 'KEAMANAN AKUN')
@section('header_title', 'Pemulihan Kata Sandi')
@section('header_subtitle', 'Verifikasi identitas pengguna Smart Room Dryer Hanjeli')

@section('content')
    <div class="email-greeting">Halo, {{ $name }}!</div>
    
    <p class="email-paragraph">
        Kami menerima permintaan untuk mereset kata sandi akun sistem Smart Dryer Hanjeli Anda. Gunakan kode OTP 6-digit di bawah ini untuk melanjutkan verifikasi:
    </p>

    <!-- OTP Code Display -->
    <div class="otp-wrapper">
        <span class="otp-tag">KODE OTP VERIFIKASI</span>
        <div class="otp-number">{{ $otpCode }}</div>
        <div class="otp-timer">Berlaku selama <strong>{{ $expiryMinutes }} menit</strong> ke depan.</div>
    </div>
@endsection

@section('notice')
    <div class="alert-box alert-box-warning">
        <strong>Pemberitahuan Keamanan:</strong> Jangan berikan kode ini kepada siapapun termasuk petugas admin atau pengelola sistem. Jika Anda tidak melakukan permintaan ini, abaikan email ini dan kata sandi Anda akan tetap aman.
    </div>
@endsection
