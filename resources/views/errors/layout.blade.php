<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/assets/img/hanjeli.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Terjadi Kesalahan') | Smart Room Dryer Hanjeli</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0D631B;
            --primary-hover: #15803D;
            --bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-title: #0F172A;
            --text-body: #475569;
            --text-muted: #64748B;
            --border: #E2E8F0;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0F172A;
                --card-bg: #1E293B;
                --text-title: #F8FAFC;
                --text-body: #CBD5E1;
                --text-muted: #94A3B8;
                --border: #334155;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            pointer-events: none;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, rgba(13, 99, 27, 0.12) 0%, rgba(13, 99, 27, 0) 70%);
        }

        .error-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 540px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
                        padding: 40px 32px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .brand-logo-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: var(--primary);
            letter-spacing: -0.01em;
        }

        .error-code-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, #0D631B 0%, #15803D 50%, #4ADE80 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }

        .error-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(13, 99, 27, 0.1);
            color: var(--primary);
            margin-bottom: 16px;
        }

        .error-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .error-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 440px;
        }

        .diagnostic-card {
            width: 100%;
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            text-align: left;
            font-size: 13px;
        }

        .action-group {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        @media (min-width: 480px) {
            .action-group {
                flex-direction: row;
                justify-content: center;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #0D631B;
            color: #FFFFFF;
                    }

        .btn-primary:hover {
            background: #15803D;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-title);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        .error-footer {
            margin-top: 28px;
            font-size: 12px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            padding-top: 16px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <div class="error-card">
        <div class="brand-logo-row">
            <svg width="28" height="28" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="22" fill="#0D631B" />
                <path d="M16 28C16 22 24 14 24 14C24 14 32 22 32 28C32 32.4183 28.4183 36 24 36C19.5817 36 16 32.4183 16 28Z" fill="#86EFAC" />
                <path d="M24 14V36" stroke="#0D631B" stroke-width="2" />
            </svg>
            <span class="brand-name">Smart Room Dryer Hanjeli</span>
        </div>

        <div class="error-code-badge">@yield('code', '404')</div>
        <div class="error-tag">@yield('tag', 'Pemberitahuan Sistem')</div>

        <h1 class="error-title">@yield('heading', 'Terjadi Kesalahan')</h1>
        <p class="error-desc">@yield('message', 'Permintaan tidak dapat diproses saat ini.')</p>

        @yield('details')

        <div class="action-group">
            <a href="/" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
            <button onclick="history.back()" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"></path>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Halaman Sebelumnya</span>
            </button>
        </div>

        <div class="error-footer">
            Smart Room Dryer Hanjeli • Desa Wisata Hanjeli & CoE STAS-RG
        </div>
    </div>
</body>
</html>
