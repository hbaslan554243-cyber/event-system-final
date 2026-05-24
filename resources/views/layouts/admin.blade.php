<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — EventPro Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
    <style>
        body { background: #f0f2f5; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e293b; position: fixed; top: 0; left: 0; z-index: 1000; overflow-y: auto; transition: transform .3s; }
        .sidebar-brand { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-nav .nav-link { color: #94a3b8; padding: .6rem 1.5rem; border-radius: 0; font-size: .875rem; display: flex; align-items: center; gap: .6rem; transition: all .2s; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color: #fff; background: rgba(255,255,255,.08); border-left: 3px solid #6366f1; }
        .sidebar-nav .nav-section { font-size: .7rem; font-weight: 700; color: #475569; letter-spacing: .08em; padding: 1rem 1.5rem .25rem; text-transform: uppercase; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .75rem 1.5rem; position: sticky; top: 0; z-index: 999; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); transition: transform .2s; }
        .stat-card:hover { transform: translateY(-2px); }
        @media (max-width: 991px) { .sidebar { transform: translateX(-100%); } .sidebar.show { transform: translateX(0); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="text-white text-decoration-none fw-bold fs-5">
            <i class="bi bi-calendar-event-fill text-indigo-400 me-2" style="color:#818cf8"></i>EventPro
        </a>
        <div class="text-muted small mt-1">
            @if(auth()->user()->isAdmin()) Admin Panel
            @else Organizer Panel
            @endif
        </div>
    </div>

    <nav class="sidebar-nav mt-2">
        @if(auth()->user()->isAdmin())
            <div class="nav-section">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Events
            </a>
            <a href="{{ route('admin.venues.index') }}" class="nav-link {{ request()->routeIs('admin.venues.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Venues
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a href="{{ route('admin.resources.index') }}" class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Resources
            </a>

            <div class="nav-section">Analytics</div>
            <a href="{{ route('admin.reports.revenue') }}" class="nav-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}">
                <i class="bi bi-currency-dollar"></i> Revenue
            </a>
            <a href="{{ route('admin.reports.attendees') }}" class="nav-link {{ request()->routeIs('admin.reports.attendees') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Attendees
            </a>
            <a href="{{ route('admin.reports.feedback') }}" class="nav-link {{ request()->routeIs('admin.reports.feedback') ? 'active' : '' }}">
                <i class="bi bi-star"></i> Feedback
            </a>
        @else
            <div class="nav-section">Overview</div>
            <a href="{{ route('organizer.dashboard') }}" class="nav-link {{ request()->routeIs('organizer.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Events</div>
            <a href="{{ route('organizer.events.index') }}" class="nav-link {{ request()->routeIs('organizer.events.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> My Events
            </a>
            <a href="{{ route('organizer.events.create') }}" class="nav-link">
                <i class="bi bi-plus-circle"></i> Create Event
            </a>
        @endif

        <div class="nav-section">Account</div>
        <a href="{{ route('profile') }}" class="nav-link">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
</aside>

{{-- Main Content --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="34" height="34">
                    <div class="d-none d-md-block">
                        <div class="fw-semibold small lh-1">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:.7rem">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    </div>

    {{-- Page --}}
    <div class="p-4">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
