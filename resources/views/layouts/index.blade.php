<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            background: #e8e8e8;
            color: #333;
            min-height: 100vh;
        }

        /* ── Top Nav ─────────────────────────────────────────── */
        .topnav {
            background: #3a3a3a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: 36px;
        }
        .topnav-brand {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            text-decoration: none;
        }
        .topnav-brand .logo-box {
            background: #e07b00;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }
        .topnav-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .topnav-right a {
            color: #ccc;
            text-decoration: none;
            font-size: 12px;
        }
        .topnav-right a:hover { color: #fff; }
        .topnav-right .nav-link-active { color: #fff; }
        .logout-btn {
            background: none;
            border: 1px solid #888;
            color: #ccc;
            padding: 3px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .logout-btn:hover { border-color: #ccc; color: #fff; }

        /* ── Page wrapper ────────────────────────────────────── */
        .page-wrapper {
            max-width: 980px;
            margin: 20px auto;
            padding: 0 12px;
        }

        /* ── Panel / Card ────────────────────────────────────── */
        .panel {
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            margin-bottom: 18px;
        }
        .panel-header {
            background: #f5f5f5;
            border-bottom: 1px solid #d0d0d0;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 4px 4px 0 0;
        }
        .panel-title {
            font-weight: bold;
            font-size: 13px;
            color: #333;
        }
        .panel-body { padding: 14px; }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert {
            padding: 8px 12px;
            border-radius: 3px;
            margin-bottom: 12px;
            font-size: 12px;
        }
        .alert-success { background: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
        .alert-error   { background: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
        .alert-info    { background: #d9edf7; color: #31708f; border: 1px solid #bce8f1; }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 3px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 12px;
            font-weight: normal;
            text-decoration: none;
            line-height: 1.4;
            white-space: nowrap;
        }
        .btn-orange {
            background: #e07b00;
            border-color: #c06a00;
            color: #fff;
        }
        .btn-orange:hover { background: #c96f00; }
        .btn-blue {
            background: #4a90d9;
            border-color: #357abd;
            color: #fff;
        }
        .btn-blue:hover { background: #357abd; }
        .btn-green {
            background: #5cb85c;
            border-color: #4cae4c;
            color: #fff;
        }
        .btn-green:hover { background: #4cae4c; }
        .btn-red {
            background: #d9534f;
            border-color: #d43f3a;
            color: #fff;
        }
        .btn-red:hover { background: #c9302c; }
        .btn-gray {
            background: #f5f5f5;
            border-color: #ccc;
            color: #333;
        }
        .btn-gray:hover { background: #e8e8e8; }
        .btn-sm { padding: 3px 8px; font-size: 11px; }

        /* ── Forms ───────────────────────────────────────────── */
        .form-row {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .form-group { margin-bottom: 10px; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #555;
        }
        .form-control {
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 12px;
            width: 100%;
            color: #333;
        }
        .form-control:focus { outline: none; border-color: #4a90d9; box-shadow: 0 0 0 2px rgba(74,144,217,.2); }
        .form-control.wide { min-width: 320px; }
        .form-control-inline { width: auto; }
        select.form-control { background: #fff; }
        .field-error { color: #a94442; font-size: 11px; margin-top: 2px; }

        /* ── Tables ──────────────────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .data-table th {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
            padding: 7px 10px;
            text-align: left;
            font-weight: bold;
            color: #555;
            white-space: nowrap;
        }
        .data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafafa; }
        .data-table .url-cell {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .data-table a { color: #4a90d9; text-decoration: none; }
        .data-table a:hover { text-decoration: underline; }

        /* ── Badge ───────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-superadmin { background: #fcf8e3; color: #8a6d3b; border: 1px solid #faebcc; }
        .badge-admin      { background: #d9edf7; color: #31708f; border: 1px solid #bce8f1; }
        .badge-member     { background: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }

        /* ── Pagination ──────────────────────────────────────── */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0 2px;
            font-size: 11px;
            color: #777;
        }
        .pagination-links { display: flex; gap: 4px; }
        .pagination-links a, .pagination-links span {
            border: 1px solid #ddd;
            padding: 3px 8px;
            border-radius: 3px;
            color: #4a90d9;
            text-decoration: none;
            font-size: 11px;
        }
        .pagination-links span.active-page {
            background: #4a90d9;
            color: #fff;
            border-color: #4a90d9;
        }
        .pagination-links span.disabled { color: #ccc; }

        /* ── Filter bar ──────────────────────────────────────── */
        .filter-bar {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .filter-bar select, .filter-bar input {
            font-size: 11px;
            padding: 3px 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* ── Divider ─────────────────────────────────────────── */
        hr.divider { border: none; border-top: 1px solid #eee; margin: 12px 0; }

        /* ── Generate URL row ────────────────────────────────── */
        .generate-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 14px;
        }
        .generate-row input { flex: 1; }

        /* ── Stats cell ──────────────────────────────────────── */
        .stat-num { font-weight: bold; color: #333; }

        /* ── Login page ──────────────────────────────────────── */
        .login-wrapper {
            max-width: 340px;
            margin: 60px auto;
            padding: 0 12px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 16px;
        }
        .login-brand .logo-box {
            display: inline-block;
            background: #e07b00;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 3px;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .login-brand h2 { font-size: 15px; color: #333; }
    </style>
</head>
<body>

@auth
<nav class="topnav">
    <a class="topnav-brand" href="{{ route('dashboard') }}">
        <span class="logo-box">URL</span>
          Shortener
    </a>
    <div class="topnav-right">
       <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>
        <a href="{{ route('short-urls.index') }}" class="{{ request()->routeIs('short-urls.*') ? 'nav-link-active' : '' }}">Short URLs</a>
        @if(!auth()->user()->isSuperAdmin())
            <a href="{{ route('invitations.create') }}" class="{{ request()->routeIs('invitations.create') ? 'nav-link-active' : '' }}">Invite</a>
        @else
            <a href="{{ route('invitations.create') }}" class="{{ request()->routeIs('invitations.create') ? 'nav-link-active' : '' }}">Invite Client</a>
        @endif
        <span style="color:#888; font-size:12px;">
            {{ auth()->user()->name }}
            <span class="badge badge-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span>
        </span>
        <form action="{{ route('post.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout →</button>
        </form>
    </div>
</nav>
@endauth

<div class="page-wrapper">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</div>

</body>
</html>
