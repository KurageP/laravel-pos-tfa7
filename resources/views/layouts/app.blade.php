<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Laravel POS</title>

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f3f5f9;
            color: #1e293b;
            font-family: Arial, sans-serif;
        }
        header {
            background: #13243a;
            color: white;
            padding: 24px max(24px, calc((100% - 1100px) / 2));
        }
        .brand { font-size: 24px; font-weight: bold; }
        .subtitle { color: #b8c6d9; margin: 8px 0 24px; }
        nav { display: flex; gap: 12px; }
        nav a {
            color: #dbeafe;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
        }
        nav a:hover { background: #263e5b; }
        nav a.active { background: #2563eb; color: white; }
        main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 24px;
        }
        h1 { margin-bottom: 8px; }
        .description { color: #64748b; margin-bottom: 24px; }
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
        }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
        }
        th {
            background: #f8fafc;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }
        footer {
            text-align: center;
            color: #64748b;
            padding: 24px;
            font-size: 13px;
        }
        .page-heading, .form-actions, .avatar-preview { display: flex; align-items: center; gap: 16px; }
        .page-heading { justify-content: space-between; flex-wrap: wrap; margin-bottom: 24px; }
        .page-heading .description { margin-bottom: 0; }
        .button { display: inline-block; background: #2563eb; color: white; border: 1px solid #2563eb;
            border-radius: 8px; padding: 11px 16px; font: inherit; text-decoration: none; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        .button-secondary { background: white; color: #334155; border-color: #cbd5e1; }
        .button-secondary:hover { background: #f1f5f9; }
        .edit-link { color: #1d4ed8; font-weight: bold; }
        .form-card { max-width: 700px; padding: 28px; }
        .field { margin-bottom: 22px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; }
        input { display: block; width: 100%; padding: 12px; font: inherit; color: #1e293b;
            border: 1px solid #94a3b8; border-radius: 7px; background: white; }
        input[aria-invalid="true"] { border-color: #b91c1c; }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: 3px solid #2563eb; outline-offset: 3px; }
        .optional, .hint { color: #64748b; font-weight: normal; }
        .hint { font-size: 14px; line-height: 1.5; margin: 8px 0 0; }
        .field-error { color: #b91c1c; font-size: 14px; margin: 8px 0 0; }
        .alert { padding: 16px 20px; border: 1px solid; border-radius: 8px; margin-bottom: 24px; }
        .alert ul { margin: 8px 0 0; padding-left: 22px; }
        .alert-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
        .avatar { border-radius: 50%; width: 48px; height: 48px; object-fit: cover; vertical-align: middle; }
        .avatar-large { width: 80px; height: 80px; }
        .avatar-preview { margin-bottom: 16px; }
        @media (max-width: 600px) {
            main { margin-top: 24px; padding: 0 16px; }
            .form-card { padding: 20px; }
            th, td { padding: 12px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">Laravel POS</div>
        <p class="subtitle">Customer and user directory</p>

        <nav aria-label="Main navigation">
            <a href="{{ route('customers.index') }}"
               class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                Customers
            </a>

            <a href="{{ route('users.index') }}"
               class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                Users
            </a>
        </nav>
    </header>

    <main>
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <footer>IT0049 · TFA7 · Lanz Wayne Villaruel</footer>
</body>
</html>
