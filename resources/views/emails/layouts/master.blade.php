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
            padding: 32px 28px 28px 28px;
            text-align: center;
            color: #FFFFFF;
            border-bottom: 3px solid #10B981;
        }

        .header-logos-table {
            margin: 0 auto 16px auto;
        }

        .header-logo-img {
            height: 38px;
            width: auto;
            max-width: 110px;
            display: inline-block;
            vertical-align: middle;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 8px;
            border-radius: 8px;
        }

        .header-brand-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(4px);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #CBFFC2;
            margin-bottom: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-title {
            margin: 0 0 6px 0;
            font-size: 21px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.4px;
            line-height: 1.3;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .header-subtitle {
            margin: 0;
            font-size: 12.5px;
            color: #E2E8F0;
            font-weight: 400;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Content Area */
        .email-body {
            padding: 32px 28px;
            background-color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .email-greeting {
            font-size: 16.5px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .email-paragraph {
            font-size: 14px;
            line-height: 1.65;
            color: #475569;
            margin-bottom: 18px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Metric Grid / Key-Value Card */
        .metric-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 20px 0;
        }

        .metric-table {
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .metric-table td {
            padding: 7px 4px;
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
            padding: 14px 16px;
            margin: 20px 0;
            font-size: 13px;
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
            padding: 22px 18px;
            text-align: center;
            margin: 24px 0;
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
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #0D631B;
            font-family: 'Plus Jakarta Sans', monospace, sans-serif;
            padding-left: 8px;
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
            margin: 26px 0 14px 0;
        }

        .btn-action {
            display: inline-block;
            background: linear-gradient(135deg, #0D631B 0%, #15803D 100%);
            color: #FFFFFF !important;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 10px;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Simplified & Elegant Clean Modern Footer */
        .email-footer {
            background: linear-gradient(180deg, #071E27 0%, #031117 100%);
            background-color: #071E27;
            padding: 30px 24px 26px 24px;
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
            border: 1px solid rgba(52, 211, 153, 0.28);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #6EE7B7;
            margin-bottom: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-title {
            font-size: 15px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.2px;
            margin-bottom: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-logo-sub {
            font-size: 11.5px;
            color: #34D399;
            font-weight: 600;
            letter-spacing: 0.1px;
            margin-bottom: 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-contact-container {
            margin: 14px 0 16px 0;
        }

        .footer-contact-pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(52, 211, 153, 0.22);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11.5px;
            color: #E2E8F0 !important;
            text-decoration: none;
            margin: 3px 4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s ease;
        }

        .footer-contact-pill:hover {
            background: rgba(16, 185, 129, 0.18);
            border-color: #10B981;
            color: #FFFFFF !important;
        }

        .footer-nav {
            margin: 12px 0 14px 0;
        }

        .footer-nav-link {
            color: #94A3B8 !important;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 6px;
            display: inline-block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: color 0.2s ease;
        }

        .footer-nav-link:hover {
            color: #FFFFFF !important;
        }

        .footer-nav-dot {
            color: #10B981;
            font-size: 9px;
            vertical-align: middle;
            display: inline-block;
            opacity: 0.6;
            margin: 0 4px;
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.12) 50%, rgba(255, 255, 255, 0) 100%);
            border: none;
            margin: 16px auto 14px auto;
            max-width: 480px;
        }

        .footer-disclaimer {
            font-size: 10.5px;
            color: #64748B;
            line-height: 1.55;
            margin-bottom: 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-copyright {
            font-size: 11px;
            color: #94A3B8;
            font-weight: 500;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-copyright strong {
            color: #CBD5E1;
            font-weight: 700;
        }

        /* Mobile Adjustments */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 16px 8px !important; }
            .email-header { padding: 24px 16px !important; }
            .header-title { font-size: 19px !important; }
            .header-logo-img { height: 32px !important; }
            .email-body { padding: 22px 16px !important; }
            .otp-number { font-size: 28px !important; letter-spacing: 5px !important; }
            .btn-action { width: 100% !important; box-sizing: border-box !important; }
            .email-footer { padding: 24px 14px !important; }
            .footer-contact-pill { display: block !important; margin: 6px auto !important; max-width: 280px !important; }
            .footer-nav-link { margin: 2px 2px; font-size: 11px; }
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
                                    <td align="center" style="padding: 0 8px; vertical-align: middle;">
                                        <img src="{{ url('assets/img/hanjeli.png') }}" alt="Logo Desa Wisata Hanjeli" class="header-logo-img" />
                                    </td>
                                    <td style="padding: 0 4px; vertical-align: middle; color: rgba(255,255,255,0.4); font-size: 16px;">•</td>
                                    <td align="center" style="padding: 0 8px; vertical-align: middle;">
                                        <img src="{{ url('assets/img/stas.png') }}" alt="Logo STAS-RG Telkom University" class="header-logo-img" />
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
                        <div class="email-footer" style="background: linear-gradient(180deg, #071E27 0%, #031117 100%); background-color: #071E27; padding: 30px 24px 26px 24px; text-align: center; border-top: 2px solid #10B981;">
                            <div class="footer-badge" style="display: inline-block; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(52, 211, 153, 0.28); padding: 3px 10px; border-radius: 12px; font-size: 9.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #6EE7B7; margin-bottom: 8px;">SMART GREENHOUSE IOT</div>
                            <div class="footer-logo-title" style="font-size: 15px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.2px; margin-bottom: 3px;">Smart Room Dryer Hanjeli</div>
                            <div class="footer-logo-sub" style="font-size: 11.5px; color: #34D399; font-weight: 600; letter-spacing: 0.1px; margin-bottom: 16px;">Desa Wisata Hanjeli Waluran &amp; CoE STAS-RG Telkom University</div>

                            <!-- Simplified Direct Contact Pills -->
                            <div class="footer-contact-container" style="margin: 14px 0 16px 0;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
                                    <tr>
                                        <td align="center" style="padding: 3px 4px;">
                                            <a href="mailto:stas-rg@telkomuniversity.ac.id" class="footer-contact-pill" style="display: inline-block; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(52, 211, 153, 0.25); padding: 6px 14px; border-radius: 20px; font-size: 11.5px; color: #E2E8F0; text-decoration: none; font-weight: 500;">
                                                <span style="color: #34D399; margin-right: 4px;">✉</span>
                                                <span style="color: #6EE7B7; font-weight: 600;">stas-rg@telkomuniversity.ac.id</span>
                                            </a>
                                        </td>
                                        <td align="center" style="padding: 3px 4px;">
                                            <a href="https://wa.me/6285722182480" class="footer-contact-pill" style="display: inline-block; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(52, 211, 153, 0.25); padding: 6px 14px; border-radius: 20px; font-size: 11.5px; color: #E2E8F0; text-decoration: none; font-weight: 500;">
                                                <span style="color: #22C55E; margin-right: 4px;">💬</span>
                                                <span>Bantuan/WA:</span> <strong style="color: #6EE7B7; font-weight: 700;">0857-2218-2480</strong>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Quick Navigation Links -->
                            <div class="footer-nav" style="margin: 12px 0 14px 0;">
                                <a href="{{ url('/') }}" class="footer-nav-link" style="color: #94A3B8; text-decoration: none; font-size: 11.5px; font-weight: 500; padding: 2px 6px; display: inline-block;">Portal Utama</a>
                                <span class="footer-nav-dot" style="color: #10B981; font-size: 9px; vertical-align: middle; display: inline-block; opacity: 0.6; margin: 0 4px;">&bull;</span>
                                <a href="{{ url('/monitoring') }}" class="footer-nav-link" style="color: #94A3B8; text-decoration: none; font-size: 11.5px; font-weight: 500; padding: 2px 6px; display: inline-block;">Monitoring Live</a>
                                <span class="footer-nav-dot" style="color: #10B981; font-size: 9px; vertical-align: middle; display: inline-block; opacity: 0.6; margin: 0 4px;">&bull;</span>
                                <a href="{{ url('/login') }}" class="footer-nav-link" style="color: #94A3B8; text-decoration: none; font-size: 11.5px; font-weight: 500; padding: 2px 6px; display: inline-block;">Login Akun</a>
                            </div>

                            <div class="footer-divider" style="height: 1px; background: rgba(255, 255, 255, 0.1); border: none; margin: 16px auto 14px auto; max-width: 480px;"></div>

                            <!-- Automated Notification Disclaimer & Copyright -->
                            <div class="footer-disclaimer" style="font-size: 10.5px; color: #64748B; line-height: 1.55; margin-bottom: 8px;">
                                Email otomatis dari server Smart Room Dryer Hanjeli. Harap tidak membalas langsung jika berupa pesan sistem.
                            </div>
                            <div class="footer-copyright" style="font-size: 11px; color: #94A3B8; font-weight: 500;">
                                &copy; {{ date('Y') }} <strong style="color: #CBD5E1;">Smart Room Dryer</strong> &bull; CoE STAS-RG Telkom University. Seluruh hak cipta dilindungi.
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
