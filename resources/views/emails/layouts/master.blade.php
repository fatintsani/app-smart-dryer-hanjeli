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
            background-color: #F4F7F4;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1E293B;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Container Layout */
        .email-wrapper {
            width: 100% !important;
            background-color: #F4F7F4;
            padding: 40px 16px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #E2ECE2;
            box-shadow: 0 10px 30px rgba(7, 30, 39, 0.06);
        }

        /* Top Header Banner */
        .email-header {
            background: linear-gradient(145deg, #071E27 0%, #0A3319 55%, #0D631B 100%);
            padding: 34px 28px 28px 28px;
            text-align: center;
            color: #FFFFFF;
            border-bottom: 3px solid #22C55E;
            position: relative;
        }

        .header-logos-table {
            margin: 0 auto 16px auto;
        }

        .header-logo-card {
            display: inline-block;
            background: #FFFFFF;
            padding: 6px 12px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            vertical-align: middle;
        }

        .header-logo-img {
            height: 32px;
            width: auto;
            max-width: 100px;
            display: inline-block;
            vertical-align: middle;
        }

        .header-logo-divider {
            color: rgba(255, 255, 255, 0.35);
            font-size: 18px;
            padding: 0 10px;
            vertical-align: middle;
        }

        .header-brand-badge {
            display: inline-block;
            background: rgba(34, 197, 94, 0.18);
            border: 1px solid rgba(110, 231, 183, 0.35);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #CBFFC2;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-title {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.5px;
            line-height: 1.3;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-subtitle {
            margin: 0;
            font-size: 13px;
            color: #D1FAE5;
            font-weight: 400;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Content Area */
        .email-body {
            padding: 34px 30px;
            background-color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .email-greeting {
            font-size: 17px;
            font-weight: 700;
            color: #071E27;
            margin-bottom: 14px;
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
            background: #F9FCF9;
            border: 1px solid #E3EFE3;
            border-radius: 14px;
            padding: 20px 22px;
            margin: 22px 0;
        }

        .metric-table {
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .metric-table tr:not(:last-child) td {
            border-bottom: 1px solid #EDF5ED;
        }

        .metric-table td {
            padding: 9px 4px;
            font-size: 13.5px;
            vertical-align: middle;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .metric-label {
            color: #64748B;
            font-weight: 500;
            width: 42%;
        }

        .metric-value {
            color: #071E27;
            font-weight: 700;
            text-align: right;
        }

        /* Alert / Highlight Boxes */
        .alert-box {
            border-radius: 12px;
            padding: 16px 18px;
            margin: 22px 0;
            font-size: 13.5px;
            line-height: 1.55;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .alert-box-info {
            background-color: #F0FDF4;
            border: 1px solid #DCFCE7;
            border-left: 4px solid #16A34A;
            color: #166534;
        }

        .alert-box-warning {
            background-color: #FFFBEB;
            border: 1px solid #FEF3C7;
            border-left: 4px solid #F59E0B;
            color: #92400E;
        }

        .alert-box-danger {
            background-color: #FEF2F2;
            border: 1px solid #FEE2E2;
            border-left: 4px solid #DC2626;
            color: #991B1B;
        }

        /* OTP Code Showcase */
        .otp-wrapper {
            background: linear-gradient(180deg, #F0FDF4 0%, #E8F7EA 100%);
            border: 2px dashed #4ADE80;
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            margin: 26px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .otp-tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #15803D;
            margin-bottom: 10px;
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
            text-shadow: 0 2px 4px rgba(13, 99, 27, 0.12);
        }

        .otp-timer {
            font-size: 12.5px;
            color: #475569;
            margin-top: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Primary Action Button (Tactile 3D Styling) */
        .btn-action-wrapper {
            text-align: center;
            margin: 28px 0 16px 0;
        }

        .btn-action {
            display: inline-block;
            background: linear-gradient(180deg, #0D631B 0%, #084913 100%);
            color: #FFFFFF !important;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            padding: 13px 30px;
            border-radius: 12px;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: 1px solid #0D631B;
            box-shadow: 0 4px 14px rgba(13, 99, 27, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Simplified & Elegant Clean Modern Footer */
        .email-footer {
            background: linear-gradient(180deg, #071E27 0%, #031117 100%);
            background-color: #071E27;
            padding: 32px 26px 28px 26px;
            text-align: center;
            border-top: 2px solid #10B981;
            font-size: 12px;
            color: #94A3B8;
            line-height: 1.6;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(52, 211, 153, 0.3);
            padding: 4px 12px;
            border-radius: 14px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #6EE7B7;
            margin-bottom: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-title {
            font-size: 16px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-sub {
            font-size: 12px;
            color: #34D399;
            font-weight: 600;
            letter-spacing: 0.1px;
            margin-bottom: 18px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-contact-container {
            margin: 14px 0 18px 0;
        }

        .footer-contact-pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(52, 211, 153, 0.25);
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 12px;
            color: #E2E8F0 !important;
            text-decoration: none;
            margin: 4px 4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-nav {
            margin: 14px 0 16px 0;
        }

        .footer-nav-link {
            color: #94A3B8 !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 3px 8px;
            display: inline-block;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-nav-dot {
            color: #10B981;
            font-size: 10px;
            vertical-align: middle;
            display: inline-block;
            opacity: 0.6;
            margin: 0 4px;
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0) 100%);
            border: none;
            margin: 18px auto 14px auto;
            max-width: 480px;
        }

        .footer-disclaimer {
            font-size: 11px;
            color: #64748B;
            line-height: 1.55;
            margin-bottom: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-copyright {
            font-size: 11.5px;
            color: #94A3B8;
            font-weight: 500;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-copyright strong {
            color: #E2E8F0;
            font-weight: 700;
        }

        /* Mobile Adjustments */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 16px 8px !important; }
            .email-header { padding: 26px 18px !important; }
            .header-title { font-size: 20px !important; }
            .header-logo-img { height: 28px !important; }
            .email-body { padding: 24px 18px !important; }
            .otp-number { font-size: 30px !important; letter-spacing: 6px !important; }
            .btn-action { width: 100% !important; box-sizing: border-box !important; }
            .email-footer { padding: 26px 16px !important; }
            .footer-contact-pill { display: block !important; margin: 6px auto !important; max-width: 300px !important; }
            .footer-nav-link { margin: 2px 2px; font-size: 11.5px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- 1. Header Banner with Hanjeli & STAS-RG Logos -->
                        <div class="email-header">
                            <!-- Dual Logos in Header -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="header-logos-table">
                                <tr>
                                    <td align="center" style="vertical-align: middle;">
                                        <div class="header-logo-card">
                                            <img src="{{ url('assets/img/hanjeli.png') }}" alt="Logo Desa Wisata Hanjeli" class="header-logo-img" />
                                        </div>
                                    </td>
                                    <td class="header-logo-divider">•</td>
                                    <td align="center" style="vertical-align: middle;">
                                        <div class="header-logo-card">
                                            <img src="{{ url('assets/img/stas.png') }}" alt="Logo STAS-RG Telkom University" class="header-logo-img" />
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="header-brand-badge">
                                @yield('header_badge', 'SMART ROOM DRYER HANJELI')
                            </div>
                            <h1 class="header-title">@yield('header_title', 'Notifikasi Sistem')</h1>
                            <p class="header-subtitle">@yield('header_subtitle', 'Greenhouse Desa Wisata Hanjeli & CoE STAS-RG Telkom University')</p>
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

                        <!-- 3. Clean, Decluttered & Modern Professional Footer -->
                        <div class="email-footer">
                            <div class="footer-badge">SMART GREENHOUSE IOT</div>
                            <div class="footer-logo-title">Smart Room Dryer Hanjeli</div>
                            <div class="footer-logo-sub">Desa Wisata Hanjeli Waluran &amp; CoE STAS-RG Telkom University</div>

                            <!-- Simplified Direct Contact Pills -->
                            <div class="footer-contact-container">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
                                    <tr>
                                        <td align="center" style="padding: 3px 4px;">
                                            <a href="mailto:stas-rg@telkomuniversity.ac.id" class="footer-contact-pill">
                                                <span style="color: #34D399; margin-right: 4px;">✉</span>
                                                <span style="color: #6EE7B7; font-weight: 600;">stas-rg@telkomuniversity.ac.id</span>
                                            </a>
                                        </td>
                                        <td align="center" style="padding: 3px 4px;">
                                            <a href="https://wa.me/6285722182480" class="footer-contact-pill">
                                                <span style="color: #22C55E; margin-right: 4px;">💬</span>
                                                <span>Bantuan/WA:</span> <strong style="color: #6EE7B7; font-weight: 700;">0857-2218-2480</strong>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Quick Navigation Links -->
                            <div class="footer-nav">
                                <a href="{{ url('/') }}" class="footer-nav-link">Portal Utama</a>
                                <span class="footer-nav-dot">&bull;</span>
                                <a href="{{ url('/monitoring') }}" class="footer-nav-link">Monitoring Live</a>
                                <span class="footer-nav-dot">&bull;</span>
                                <a href="{{ url('/login') }}" class="footer-nav-link">Login Akun</a>
                            </div>

                            <div class="footer-divider"></div>

                            <!-- Automated Notification Disclaimer & Copyright -->
                            <div class="footer-disclaimer">
                                Email otomatis dari sistem Smart Room Dryer Hanjeli. Informasi ini ditujukan khusus bagi pengguna terdaftar.
                            </div>
                            <div class="footer-copyright">
                                &copy; {{ date('Y') }} <strong>Smart Room Dryer</strong> &bull; CoE STAS-RG Telkom University. Seluruh hak cipta dilindungi.
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
