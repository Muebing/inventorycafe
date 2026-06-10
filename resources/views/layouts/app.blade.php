<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'InventoryCafe')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #5D4037;
            --primary-light: #8D6E63;
            --primary-lighter: #D7CCC8;
            --primary-bg: #EFEBE9;
            --accent: #FF8F00;
            --sidebar-width: 250px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, #3E2723 100%);
            color: #fff;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 1.25rem 1.25rem .75rem;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand i { font-size: 1.5rem; color: var(--accent); }
        .sidebar-nav { flex: 1; padding: .5rem 0; overflow-y: auto; }
        .sidebar-heading {
            padding: .75rem 1.25rem .25rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.5);
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1.25rem;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .9rem;
            transition: all .2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar-nav a.active {
            color: #fff;
            background: rgba(255,255,255,.12);
            border-left-color: var(--accent);
        }
        .sidebar-nav a i { width: 1.25rem; text-align: center; font-size: 1rem; }
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .topbar h5 { margin: 0; color: var(--primary); font-weight: 600; }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #555;
            font-size: .9rem;
        }
        .topbar-user .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--primary-lighter);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .85rem;
        }
        .content {
            flex: 1;
            padding: 1.5rem;
        }
        .card-custom {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card .stat-icon { font-size: 2.5rem; opacity: .3; }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label { font-size: .85rem; opacity: .85; }
        .stat-primary { background: linear-gradient(135deg, #5D4037, #8D6E63); }
        .stat-warning { background: linear-gradient(135deg, #FF8F00, #FFB300); }
        .stat-danger { background: linear-gradient(135deg, #C62828, #E53935); }
        .btn-custom-primary {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .4rem 1rem;
            font-size: .85rem;
        }
        .btn-custom-primary:hover { background: #4E342E; color: #fff; }
        .btn-custom-warning {
            background: #FFF3E0;
            color: #E65100;
            border: none;
            border-radius: 8px;
            padding: .4rem .8rem;
            font-size: .8rem;
        }
        .btn-custom-warning:hover { background: #FFE0B2; }
        .btn-custom-danger {
            background: #FFEBEE;
            color: #C62828;
            border: none;
            border-radius: 8px;
            padding: .4rem .8rem;
            font-size: .8rem;
        }
        .btn-custom-danger:hover { background: #FFCDD2; }
        .table-custom { font-size: .9rem; }
        .table-custom thead th {
            background: var(--primary-bg);
            color: var(--primary);
            font-weight: 600;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: none;
            padding: .7rem .75rem;
        }
        .table-custom td { padding: .65rem .75rem; vertical-align: middle; }
        .modal-custom .modal-header {
            background: var(--primary);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.25rem;
        }
        .modal-custom .modal-header .btn-close { filter: brightness(0) invert(1); }
        .modal-custom .modal-content { border-radius: 12px; border: none; }
        .modal-custom .modal-footer { border-top: 1px solid #eee; padding: .75rem 1.25rem; }
        .footer {
            text-align: center;
            padding: 1rem;
            font-size: .8rem;
            color: #999;
            margin-top: auto;
        }
        .badge-custom {
            padding: .35em .65em;
            border-radius: 6px;
            font-weight: 500;
            font-size: .75rem;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
        }
        .pagination { margin: 0; }
        .page-link {
            border: none;
            color: var(--primary);
            border-radius: 6px !important;
            margin: 0 2px;
        }
        .page-item.active .page-link { background: var(--primary); }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: .9rem;
            padding: .45rem .75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(93,64,55,.12);
        }
        .table-responsive { border-radius: 8px; }
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.25rem;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main { margin-left: 0; }
            .sidebar-toggle { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-cup-hot-fill"></i>
            <span>InventoryCafe</span>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-heading">Menu</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <div class="sidebar-heading">Master Data</div>
            <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Barang
            </a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Kategori
            </a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Supplier
            </a>

            <div class="sidebar-heading">Transaksi</div>
            <a href="{{ route('stock-in.index') }}" class="{{ request()->routeIs('stock-in.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-down-circle-fill"></i> Barang Masuk
            </a>
            <a href="{{ route('stock-out.index') }}" class="{{ request()->routeIs('stock-out.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-circle-fill"></i> Barang Keluar
            </a>
            <a href="{{ route('stock-adjustments.index') }}" class="{{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> Adjustment
            </a>

            <div class="sidebar-heading">Laporan</div>
            <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Log Activity
            </a>
        </nav>
    </div>

    <div class="main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:.75rem">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5>@yield('page_title', 'Dashboard')</h5>
            </div>
            <div class="topbar-user">
                <span>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="border-color:#ddd;font-size:.8rem">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show alert-custom" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} InventoryCafe
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @stack('scripts')
    <script>
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768 && sidebar.classList.contains('show') && !sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
