@extends('errors.layout')

@section('title', 'Kesalahan Server (500)')
@section('code', '500')
@section('tag', '500 • Internal Server Error')
@section('heading', 'Terjadi Gangguan pada Server')
@section('message', 'Server atau database sedang mengalami kendala sementara atau sedang dalam proses pemeliharaan. Silakan coba sesaat lagi.')

@section('details')
<div class="diagnostic-card">
    <div style="font-weight: 600; color: #DC2626; margin-bottom: 4px;">Petunjuk Pemulihan:</div>
    <div>• Pastikan server backend Laravel berjalan aktif di <code>http://localhost:8000</code></div>
    <div>• Pastikan database MySQL terhubung pada port <code>3306</code></div>
</div>
@endsection
