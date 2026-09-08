<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'Smart Room Dryer Hanjeli')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!--[if mso]>
    <style>
      body, table, td, p, a, h1, h2, h3, h4, span, div { font-family: 'Plus Jakarta Sans', Arial, sans-serif !important; }
    </style>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap');

        /* Base Reset */
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            box-sizing: border-box;
        }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #F1F5F9;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Container Layout */
        .email-wrapper {
            width: 100% !important;
            background-color: #F1F5F9;
            padding: 40px 16px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Top Header Banner */
        .email-header {
            background: linear-gradient(135deg, #071E27 0%, #0D631B 100%);
            padding: 36px 32px 32px 32px;
            text-align: center;
            color: #FFFFFF;
            border-bottom: 3px solid #10B981;
        }

        .header-brand-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(4px);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #CBFFC2;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-title {
            margin: 0 0 6px 0;
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.4px;
            line-height: 1.3;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-subtitle {
            margin: 0;
            font-size: 13px;
            color: #E2E8F0;
            font-weight: 400;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Content Area */
        .email-body {
            padding: 36px 32px;
            background-color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .email-greeting {
            font-size: 17px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .email-paragraph {
            font-size: 14.5px;
            line-height: 1.65;
            color: #475569;
            margin-bottom: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Metric Grid / Key-Value Card */
        .metric-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }

        .metric-table {
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .metric-table td {
            padding: 8px 6px;
            font-size: 13.5px;
            vertical-align: middle;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .metric-label {
            color: #64748B;
            font-weight: 500;
            width: 45%;
        }

        .metric-value {
            color: #0F172A;
            font-weight: 700;
            text-align: right;
        }

        /* Alert / Highlight Boxes */
        .alert-box {
            border-radius: 10px;
            padding: 16px 18px;
            margin: 22px 0;
            font-size: 13.5px;
            line-height: 1.55;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .alert-box-info {
            background-color: #F0FDF4;
            border-left: 4px solid #16A34A;
            color: #166534;
        }

        .alert-box-warning {
            background-color: #FFFBEB;
            border-left: 4px solid #F59E0B;
            color: #92400E;
        }

        .alert-box-danger {
            background-color: #FEF2F2;
            border-left: 4px solid #DC2626;
            color: #991B1B;
        }

        /* OTP Code Showcase */
        .otp-wrapper {
            background: linear-gradient(180deg, #F0FDF4 0%, #DCFCE7 100%);
            border: 2px dashed #4ADE80;
            border-radius: 14px;
            padding: 24px 20px;
            text-align: center;
            margin: 28px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .otp-tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            color: #15803D;
            margin-bottom: 8px;
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .otp-number {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #0D631B;
            font-family: 'Plus Jakarta Sans', monospace, sans-serif;
            padding-left: 10px;
            line-height: 1.1;
        }

        .otp-timer {
            font-size: 12px;
            color: #4B5563;
            margin-top: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Primary Action Button */
        .btn-action-wrapper {
            text-align: center;
            margin: 30px 0 16px 0;
        }

        .btn-action {
            display: inline-block;
            background: linear-gradient(135deg, #0D631B 0%, #15803D 100%);
            color: #FFFFFF !important;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            padding: 13px 32px;
            border-radius: 10px;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Rich Standardized Footer */
        .email-footer {
            background-color: #0F172A;
            padding: 32px 28px;
            text-align: center;
            border-top: 3px solid #10B981;
            font-size: 12px;
            color: #94A3B8;
            line-height: 1.7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-title {
            font-size: 15px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.2px;
            margin-bottom: 4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-sub {
            font-size: 12px;
            color: #34D399;
            font-weight: 600;
            margin-bottom: 18px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 18px 0;
            border: none;
        }

        /* Social Media Buttons */
        .social-table {
            margin: 0 auto 20px auto;
        }

        .social-btn {
            display: inline-block;
            padding: 7px 12px;
            margin: 3px 4px;
            background-color: #1E293B;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #E2E8F0 !important;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .social-btn:hover {
            background-color: #0D631B;
            border-color: #10B981;
            color: #FFFFFF !important;
        }

        /* Quick Navigation Links */
        .footer-nav {
            margin: 12px 0 16px 0;
        }

        .footer-nav a {
            color: #CBD5E1 !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            margin: 0 8px;
            display: inline-block;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-address-box {
            font-size: 11.5px;
            color: #94A3B8;
            line-height: 1.6;
            margin-bottom: 14px;
        }

        .footer-contact-row {
            font-size: 11.5px;
            color: #CBD5E1;
            margin-bottom: 16px;
        }

        .footer-contact-row a {
            color: #6EE7B7 !important;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-disclaimer {
            font-size: 10.5px;
            color: #64748B;
            line-height: 1.5;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 14px;
            margin-top: 14px;
        }

        .footer-copyright {
            font-size: 11px;
            color: #94A3B8;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Mobile Adjustments */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 16px 8px !important; }
            .email-header { padding: 28px 20px !important; }
            .header-title { font-size: 20px !important; }
            .email-body { padding: 24px 18px !important; }
            .otp-number { font-size: 30px !important; letter-spacing: 6px !important; }
            .btn-action { width: 100% !important; box-sizing: border-box !important; }
            .email-footer { padding: 24px 16px !important; }
            .social-btn { display: inline-block; margin: 3px 2px; padding: 6px 10px; font-size: 11px; }
            .footer-nav a { margin: 0 4px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- 1. Header Banner -->
                        <div class="email-header">
                            <div class="header-brand-badge">
                                @yield('header_badge', 'SMART ROOM DRYER HANJELI')
                            </div>
                            <h1 class="header-title">@yield('header_title', 'Notifikasi Sistem')</h1>
                            <p class="header-subtitle">@yield('header_subtitle', 'Green House Desa Wisata Hanjeli & CoE STAS-RG Telkom University')</p>
                        </div>

                        <!-- 2. Main Content Body -->
                        <div class="email-body">
                            @yield('content')

                            @hasSection('action')
                                <div class="btn-action-wrapper">
                                    @yield('action')
                                </div>
                            @endif

                            @hasSection('notice')
                                @yield('notice')
                            @endif
                        </div>

                        <!-- 3. Rich Modern & Professional Footer with Social Media -->
                        <div class="email-footer">
                            <div class="footer-logo-title">🌾 Smart Room Dryer Hanjeli</div>
                            <div class="footer-logo-sub">Sistem Otomasi & Telemetri IoT Greenhouse Pengeringan Pascapanen</div>

                            <!-- Social Media & Official Channels Buttons -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="social-table">
                                <tr>
                                    <td align="center">
                                        <a href="https://www.stas-rg.com" target="_blank" rel="noopener noreferrer" class="social-btn">
                                            🌐 Website
                                        </a>
                                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-btn">
                                            📸 Instagram
                                        </a>
                                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="social-btn">
                                            🎥 YouTube
                                        </a>
                                        <a href="https://wa.me/6285722182480" target="_blank" rel="noopener noreferrer" class="social-btn">
                                            💬 WhatsApp
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Navigation Links -->
                            <div class="footer-nav">
                                <a href="{{ url('/') }}">Portal Utama</a>
                                <span style="color: #475569;">•</span>
                                <a href="{{ url('/monitoring') }}">Monitoring Live</a>
                                <span style="color: #475569;">•</span>
                                <a href="{{ url('/#panduan') }}">Panduan SOP</a>
                                <span style="color: #475569;">•</span>
                                <a href="{{ url('/login') }}">Login Sistem</a>
                            </div>

                            <hr class="footer-divider">

                            <!-- Location & Research Collaboration Details -->
                            <div class="footer-address-box">
                                📍 <strong>Lokasi Fasilitas:</strong> Desa Wisata Hanjeli, Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kab. Sukabumi, Jawa Barat 43175<br>
                                🏛️ <strong>Kolaborasi Riset:</strong> Center of Excellence (CoE) STAS-RG Telkom University & Kelompok Tani Desa Wisata Hanjeli
                            </div>

                            <!-- Direct Contacts -->
                            <div class="footer-contact-row">
                                ✉️ Email: <a href="mailto:userstas@mail.com">userstas@mail.com</a> &nbsp;|&nbsp; 
                                📞 Kontak/Telp: <a href="tel:085722182480">0857-2218-2480</a>
                            </div>

                            <!-- Automated Notification Disclaimer & Copyright -->
                            <div class="footer-disclaimer">
                                Email ini dikirimkan secara otomatis oleh server IoT Smart Room Dryer Hanjeli. Harap tidak membalas langsung ke alamat email ini jika berupa pesan otomatis sistem.
                            </div>
                            <div class="footer-copyright">
                                Hak Cipta &copy; {{ date('Y') }} Smart Room Dryer — CoE STAS-RG Telkom University. Seluruh hak cipta dilindungi undang-undang.
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
