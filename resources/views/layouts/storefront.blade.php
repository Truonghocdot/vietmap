<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('{{ asset('fonts/inter-400-vn.woff2') }}') format('woff2');
        }
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('{{ asset('fonts/inter-600-vn.woff2') }}') format('woff2');
        }
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 800;
            font-display: swap;
            src: url('{{ asset('fonts/inter-800-vn.woff2') }}') format('woff2');
        }

        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe3ef;
            --brand: #059669;
            --brand-dark: #047857;
            --accent: #2563eb;
            --warn: #b45309;
            --warn-bg: #fff7ed;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
            --ok-bg: #ecfdf5;
            --ok-text: #166534;
            --shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
            --radius: 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(180deg, #edf6f1 0%, var(--bg) 180px);
            color: var(--text);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-shell {
            min-height: 100vh;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(219, 227, 239, 0.9);
        }

        .topbar-inner,
        .page {
            width: min(1100px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            object-fit: cover;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--brand);
        }

        .brand-sub {
            font-size: 12px;
            color: var(--muted);
        }

        .topbar-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .topbar-link {
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.85);
        }

        .topbar-link.primary {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            border-color: transparent;
        }

        .page {
            padding: 32px 0 48px;
        }

        .grid {
            display: grid;
            gap: 24px;
        }

        .grid.two {
            grid-template-columns: minmax(0, 1fr) 330px;
            align-items: start;
        }

        .card {
            background: var(--card);
            border: 1px solid rgba(219, 227, 239, 0.95);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-body {
            padding: 28px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(5, 150, 105, 0.08);
            color: var(--brand);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .page-title {
            margin: 18px 0 10px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.05;
        }

        .page-subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .stack {
            display: grid;
            gap: 16px;
        }

        .summary {
            display: grid;
            gap: 12px;
            margin-top: 24px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 18px;
            background: #f8fafc;
            border: 1px solid #e6edf5;
            border-radius: 14px;
        }

        .summary-row.total {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border-color: #a7f3d0;
        }

        .label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .value {
            text-align: right;
            font-size: 14px;
            font-weight: 700;
        }

        .value.price {
            color: var(--brand);
            font-size: 20px;
            font-weight: 800;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.6;
            border: 1px solid transparent;
        }

        .alert.warning {
            background: var(--warn-bg);
            color: var(--warn);
            border-color: #fdba74;
        }

        .alert.error {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: #fecaca;
        }

        .alert.success {
            background: var(--ok-bg);
            color: var(--ok-text);
            border-color: #86efac;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            font-size: 14px;
            font-weight: 700;
        }

        .field small {
            color: var(--muted);
        }

        .input,
        .textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: var(--text);
            font: inherit;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 52px;
            padding: 14px 18px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
        }

        .button.primary {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            box-shadow: 0 16px 30px rgba(5, 150, 105, 0.2);
        }

        .button.secondary {
            background: #eff6ff;
            color: var(--accent);
        }

        .button.ghost {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--muted);
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .meta-card {
            padding: 22px;
            border-radius: 18px;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
        }

        .meta-card h3 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        .meta-list {
            display: grid;
            gap: 12px;
        }

        .meta-item strong {
            display: block;
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .meta-item span {
            font-size: 15px;
            font-weight: 700;
            word-break: break-word;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pill.pending {
            background: #fff7ed;
            color: #b45309;
        }

        .status-pill.paid,
        .status-pill.fulfilled {
            background: #ecfdf5;
            color: #166534;
        }

        .status-pill.expired,
        .status-pill.failed {
            background: #fef2f2;
            color: #b91c1c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e6edf5;
            text-align: left;
            font-size: 14px;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .muted {
            color: var(--muted);
        }

        @media (max-width: 920px) {
            .grid.two {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .topbar-inner,
            .page {
                width: min(100% - 24px, 1100px);
            }

            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .card-body {
                padding: 22px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="site-shell">
        <header class="topbar">
            <div class="topbar-inner">
                <a href="{{ route('storefront.home') }}" class="brand">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Vietmap">
                    <div>
                        <div class="brand-name">ThuêVietMap.vn</div>
                        <div class="brand-sub">SePay + auto nhả key qua mail</div>
                    </div>
                </a>
                <nav class="topbar-links">
                    <a class="topbar-link" href="{{ route('orders.history') }}">Lịch sử 30 ngày</a>
                    <a class="topbar-link" href="/blog">Blog</a>
                    <a class="topbar-link primary" href="{{ route('storefront.home') }}">Về trang chủ</a>
                </nav>
            </div>
        </header>

        <main class="page">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
