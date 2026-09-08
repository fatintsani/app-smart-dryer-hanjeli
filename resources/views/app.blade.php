<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/assets/img/hanjeli.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart Room Dryer Hanjeli') }}</title>
    
    <!-- PWA & Mobile Meta Tags -->
    <meta name="theme-color" content="#0D631B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="Smart Dryer" />
    <meta name="description" content="Sistem IoT Cerdas Monitoring & Pengeringan Hanjeli - Desa Wisata Hanjeli Waluran & CoE STAS-RG" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Google Identity Services (GIS) for Google Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
