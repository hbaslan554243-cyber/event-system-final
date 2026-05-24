<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EventPro') — EventPro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --ep-primary:    #2563eb;
            --ep-primary-dk: #1d4ed8;
            --ep-primary-lt: #eff6ff;
            --ep-bg:         #f9fafb;
            --ep-text:       #111827;
            --ep-soft:       #6b7280;
            --ep-border:     #e5e7eb;
            --ep-nav-h:      68px;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--ep-bg); color: var(--ep-text); margin: 0; }

        /* ═══════════════════════════════════════
           HERO PHOTO WRAPPER
        ═══════════════════════════════════════ */
        .ep-hero-wrap {
            position: relative;
            background:
                linear-gradient(to bottom, rgba(8,4,24,.6) 0%, rgba(8,4,24,.35) 65%, rgba(8,4,24,.15) 100%),
                url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1800&q=80&auto=format&fit=crop')
                center center / cover no-repeat;
            padding-bottom: 60px;
        }
        .ep-hero-wrap::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 80px;
            background: var(--ep-bg);
            border-radius: 50% 50% 0 0 / 80px 80px 0 0;
        }

        /* ═══════════════════════════════════════
           NAVBAR
        ═══════════════════════════════════════ */
        .ep-nav {
            height: var(--ep-nav-h);
            background: transparent;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: background .3s, backdrop-filter .3s;
        }
        .ep-nav.scrolled {
            background: rgba(8,4,24,.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .ep-nav .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .ep-brand {
            font-size: 20px; font-weight: 800;
            color: #fff; text-decoration: none;
            display: flex; align-items: center; gap: 10px;
            flex-shrink: 0; letter-spacing: -.3px;
        }
        .ep-brand-icon {
            width: 36px; height: 36px;
            background: var(--ep-primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 17px;
        }
        .ep-nav-links {
            display: flex; align-items: center; gap: 2px;
            list-style: none; margin: 0; padding: 0;
        }
        .ep-nav-links .nav-link {
            font-size: 14px; font-weight: 500;
            color: rgba(255,255,255,.82);
            padding: 7px 14px; border-radius: 8px;
            transition: all .15s; text-decoration: none;
            display: flex; align-items: center; gap: 6px;
        }
        .ep-nav-links .nav-link:hover,
        .ep-nav-links .nav-link.active {
            color: #fff; background: rgba(255,255,255,.12);
        }
        .ep-nav-right {
            display: flex; align-items: center; gap: 8px;
            list-style: none; margin: 0; padding: 0;
        }
        .btn-ep-ghost {
            font-size: 14px; font-weight: 500;
            color: rgba(255,255,255,.85);
            background: transparent; border: none;
            padding: 7px 14px; border-radius: 8px;
            text-decoration: none; transition: all .15s;
        }
        .btn-ep-ghost:hover { color: #fff; background: rgba(255,255,255,.12); }
        .btn-ep-primary {
            font-size: 14px; font-weight: 700;
            color: #fff; background: var(--ep-primary);
            border: none; padding: 9px 20px;
            border-radius: 9999px; text-decoration: none;
            transition: all .2s;
            box-shadow: 0 2px 12px rgba(37,99,235,.45);
        }
        .btn-ep-primary:hover {
            background: var(--ep-primary-dk); color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(37,99,235,.55);
        }
        .ep-avatar-btn {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.2);
            border-radius: 9999px;
            padding: 5px 14px 5px 6px;
            cursor: pointer; transition: all .15s;
            text-decoration: none; color: #fff;
        }
        .ep-avatar-btn:hover {
            background: rgba(255,255,255,.2); color: #fff;
            border-color: rgba(255,255,255,.35);
        }
        .ep-avatar-btn img {
            width: 28px; height: 28px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(255,255,255,.3);
        }
        .ep-avatar-name {
            font-size: 13px; font-weight: 600;
            max-width: 120px;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .ep-dropdown {
            border: 1px solid var(--ep-border);
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0,0,0,.15);
            padding: 6px; min-width: 220px;
            margin-top: 8px !important;
        }
        .ep-dropdown .dropdown-header {
            font-size: 12px; color: var(--ep-soft);
            padding: 6px 10px 2px; font-weight: 400;
        }
        .ep-dropdown .dropdown-item {
            font-size: 13px; font-weight: 500;
            color: var(--ep-text);
            border-radius: 8px; padding: 9px 12px;
            display: flex; align-items: center; gap: 10px;
            transition: all .12s;
        }
        .ep-dropdown .dropdown-item:hover { background: var(--ep-primary-lt); color: var(--ep-primary); }
        .ep-dropdown .dropdown-item.text-danger:hover { background: #fef2f2; color: #dc2626; }
        .ep-dropdown hr { border-color: var(--ep-border); margin: 4px 0; }
        .ep-toggler {
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.22);
            border-radius: 8px; padding: 6px 10px;
            cursor: pointer; color: #fff; display: none;
        }
        .ep-mobile-menu {
            background: rgba(8,4,24,.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,.08);
            padding: 10px 16px 14px;
        }
        .ep-mobile-menu .nav-link {
            font-size: 15px; font-weight: 500;
            color: rgba(255,255,255,.8);
            padding: 10px 14px; border-radius: 8px;
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; transition: all .15s;
        }
        .ep-mobile-menu .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }

        /* ═══════════════════════════════════════
           HERO CONTENT
        ═══════════════════════════════════════ */
        .ep-hero-content {
            position: relative; z-index: 1;
            padding: 56px 0 96px;
        }

        /* ═══════════════════════════════════════
           FLASH TOASTS
        ═══════════════════════════════════════ */
        .ep-flash {
            position: fixed;
            top: calc(var(--ep-nav-h) + 12px);
            right: 16px; z-index: 2000;
            display: flex; flex-direction: column; gap: 8px;
            max-width: 380px; width: calc(100% - 32px);
        }
        .ep-alert {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 500;
            border: 1px solid transparent;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
            animation: slideIn .25s ease;
        }
        @keyframes slideIn {
            from { opacity:0; transform:translateX(20px); }
            to   { opacity:1; transform:translateX(0); }
        }
        .ep-alert-success { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }
        .ep-alert-danger  { background:#fef2f2; border-color:#fecaca; color:#dc2626; }
        .ep-alert-warning { background:#fffbeb; border-color:#fde68a; color:#d97706; }
        .ep-alert-icon { font-size:18px; flex-shrink:0; }
        .ep-alert-close {
            margin-left:auto; background:none; border:none;
            cursor:pointer; opacity:.5; color:inherit;
            padding:0; font-size:16px; line-height:1; transition:opacity .15s;
        }
        .ep-alert-close:hover { opacity:1; }

        /* ═══════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════ */
        .ep-footer {
            background: #0a0518;
            color: #94a3b8;
            padding: 64px 0 32px;
            margin-top: 80px;
        }
        .ep-footer-brand {
            font-size: 20px; font-weight: 800; color: #fff;
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 14px;
        }
        .ep-footer-brand-icon {
            width: 34px; height: 34px;
            background: var(--ep-primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
        }
        .ep-footer p { font-size: 14px; line-height: 1.75; }
        .ep-footer-heading {
            font-size: 12px; font-weight: 700; color: #fff;
            text-transform: uppercase; letter-spacing: .1em;
            margin-bottom: 16px;
        }
        .ep-footer-links {
            list-style: none; padding: 0; margin: 0;
            display: flex; flex-direction: column; gap: 10px;
        }
        .ep-footer-links a { font-size: 14px; color: #94a3b8; text-decoration: none; transition: color .15s; }
        .ep-footer-links a:hover { color: #fff; }
        .ep-footer-social { display: flex; gap: 10px; margin-top: 18px; }
        .ep-footer-social a {
            width: 36px; height: 36px; border-radius: 9px;
            background: #1a0f35; color: #94a3b8;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; text-decoration: none; transition: all .15s;
        }
        .ep-footer-social a:hover { background: var(--ep-primary); color: #fff; }
        .ep-footer-nl .form-control {
            background: #1a0f35; border: 1px solid #2d1f5e;
            color: #fff; border-radius: 10px 0 0 10px;
            font-size: 13px; padding: 10px 14px;
        }
        .ep-footer-nl .form-control::placeholder { color: #64748b; }
        .ep-footer-nl .form-control:focus {
            background: #1a0f35; border-color: var(--ep-primary);
            color: #fff; box-shadow: none;
        }
        .ep-footer-nl .btn {
            background: var(--ep-primary); border: none; color: #fff;
            font-size: 13px; font-weight: 600;
            padding: 10px 18px; border-radius: 0 10px 10px 0;
            transition: background .15s;
        }
        .ep-footer-nl .btn:hover { background: var(--ep-primary-dk); color: #fff; }
        .ep-footer hr { border-color: #1a0f35; margin: 40px 0 24px; }
        .ep-footer-bottom {
            display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 12px;
            font-size: 13px;
        }
        .ep-footer-bottom a { color: #64748b; text-decoration: none; transition: color .15s; }
        .ep-footer-bottom a:hover { color: #fff; }

        @media (max-width: 991px) {
            .ep-nav-links,
            .ep-nav-right { display: none !important; }
            .ep-toggler { display: flex !important; }
        }
    </style>

    {{-- Injected immediately so no flash on page load --}}
    @if(View::hasSection('hide_hero'))
    <style>
        .ep-hero-wrap {
            background: #fff !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 0 !important;
            height: auto !important;
        }
        .ep-hero-wrap::after,
        .ep-hero-wrap::before {
            display: none !important;
            height: 0 !important;
        }
        .ep-hero-content { display: none !important; }
        .ep-nav {
            background: #fff !important;
            border-bottom: 1px solid var(--ep-border) !important;
            box-shadow: 0 1px 8px rgba(0,0,0,.06) !important;
        }
        .ep-nav.scrolled {
            background: #fff !important;
            backdrop-filter: none !important;
        }
        .ep-brand { color: var(--ep-primary) !important; }
        .ep-brand-icon { background: var(--ep-primary) !important; }
        .ep-nav-links .nav-link { color: var(--ep-text) !important; }
        .ep-nav-links .nav-link:hover,
        .ep-nav-links .nav-link.active {
            color: var(--ep-primary) !important;
            background: var(--ep-primary-lt) !important;
        }
        .btn-ep-ghost { color: var(--ep-text) !important; }
        .btn-ep-ghost:hover {
            color: var(--ep-primary) !important;
            background: var(--ep-primary-lt) !important;
        }
        .ep-avatar-btn {
            background: rgba(0,0,0,.06) !important;
            border-color: rgba(0,0,0,.15) !important;
            color: var(--ep-text) !important;
        }
        .ep-avatar-btn:hover { background: rgba(0,0,0,.1) !important; }
        .ep-avatar-name { color: var(--ep-text) !important; }
        .ep-toggler {
            background: rgba(0,0,0,.06) !important;
            border-color: rgba(0,0,0,.15) !important;
            color: var(--ep-text) !important;
        }
        .ep-mobile-menu {
            background: #fff !important;
            border-bottom: 1px solid var(--ep-border) !important;
        }
        .ep-mobile-menu .nav-link { color: var(--ep-text) !important; }
        .ep-mobile-menu .nav-link:hover {
            background: var(--ep-primary-lt) !important;
            color: var(--ep-primary) !important;
        }
    </style>
    @endif

    @stack('styles')
</head>
<body>

<div class="ep-hero-wrap">

    <nav class="ep-nav" id="epNav">
        <div class="container">
            <a class="ep-brand" href="{{ url('/') }}">
                <span class="ep-brand-icon"><i class="bi bi-calendar-event-fill"></i></span>
                EventPro
            </a>

            <ul class="ep-nav-links">
                <li>
                    <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
                       href="{{ route('events.index') }}">
                        <i class="bi bi-search"></i> Browse Events
                    </a>
                </li>
            </ul>

            <ul class="ep-nav-right">
                @guest
                    <li><a class="btn-ep-ghost" href="{{ route('login') }}">Login</a></li>
                    <li><a class="btn-ep-primary" href="{{ route('register') }}">Sign Up Free</a></li>
                @else
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a class="btn-ep-ghost" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Admin Panel
                            </a>
                        </li>
                    @elseif(auth()->user()->isOrganizer())
                        <li>
                            <a class="btn-ep-ghost" href="{{ route('organizer.dashboard') }}">
                                <i class="bi bi-grid me-1"></i>Dashboard
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="btn-ep-ghost" href="{{ route('attendee.dashboard') }}">
                                <i class="bi bi-grid me-1"></i>My Events
                            </a>
                        </li>
                    @endif
                    <li class="dropdown">
                        <a class="ep-avatar-btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar">
                            <span class="ep-avatar-name">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size:10px;opacity:.6;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end ep-dropdown">
                            <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                            <li><hr></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person-circle"></i> Profile
                                </a>
                            </li>
                            @if(auth()->user()->isAttendee())
                                <li>
                                    <a class="dropdown-item" href="{{ route('attendee.tickets.index') }}">
                                        <i class="bi bi-ticket-perforated"></i> My Tickets
                                    </a>
                                </li>
                            @endif
                            <li><hr></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>

            <button class="ep-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                <i class="bi bi-list" style="font-size:22px;"></i>
            </button>
        </div>

        <div class="collapse ep-mobile-menu" id="mobileMenu">
            <a class="nav-link" href="{{ route('events.index') }}">
                <i class="bi bi-search"></i> Browse Events
            </a>
            @guest
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="bi bi-person-plus"></i> Sign Up Free
                </a>
            @else
                @if(auth()->user()->isAdmin())
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Admin Panel
                    </a>
                @elseif(auth()->user()->isOrganizer())
                    <a class="nav-link" href="{{ route('organizer.dashboard') }}">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>
                @else
                    <a class="nav-link" href="{{ route('attendee.dashboard') }}">
                        <i class="bi bi-grid"></i> My Events
                    </a>
                @endif
                <a class="nav-link" href="{{ route('profile') }}">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
                @if(auth()->user()->isAttendee())
                    <a class="nav-link" href="{{ route('attendee.tickets.index') }}">
                        <i class="bi bi-ticket-perforated"></i> My Tickets
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link w-100 text-start"
                            style="background:none;border:none;color:#f87171;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            @endguest
        </div>
    </nav>

    <div class="ep-hero-content">
        @hasSection('hero')
            @yield('hero')
        @else
            <div class="container text-white" style="padding-top:32px;">
                <div style="max-width:600px;">
                    <span style="display:inline-flex;align-items:center;gap:8px;
                                 background:rgba(37,99,235,.55);backdrop-filter:blur(8px);
                                 border:1px solid rgba(255,255,255,.18);border-radius:9999px;
                                 padding:5px 16px;font-size:13px;font-weight:600;
                                 letter-spacing:.04em;margin-bottom:22px;">
                        🎉 Discover Amazing Events
                    </span>
                    <h1 style="font-size:clamp(36px,5vw,62px);font-weight:800;
                               line-height:1.08;letter-spacing:-2px;margin-bottom:18px;">
                        Your Gateway to<br>
                        <span style="color:#60a5fa;">Unforgettable</span><br>
                        Experiences
                    </h1>
                    <p style="font-size:17px;font-weight:300;opacity:.82;
                              max-width:460px;line-height:1.72;margin-bottom:34px;">
                        Find, register, and attend events that matter to you —
                        from tech conferences to live concerts, all in one place.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('events.index') }}"
                           style="display:inline-flex;align-items:center;gap:8px;
                                  background:#2563eb;color:#fff;font-weight:700;
                                  font-size:15px;padding:13px 28px;border-radius:9999px;
                                  text-decoration:none;
                                  box-shadow:0 4px 20px rgba(37,99,235,.5);
                                  transition:all .2s;">
                            <i class="bi bi-search"></i> Browse Events
                        </a>
                        @guest
                        <a href="{{ route('register') }}"
                           style="display:inline-flex;align-items:center;gap:8px;
                                  background:rgba(255,255,255,.12);color:#fff;
                                  font-weight:600;font-size:15px;padding:13px 28px;
                                  border-radius:9999px;text-decoration:none;
                                  border:1.5px solid rgba(255,255,255,.28);
                                  backdrop-filter:blur(8px);transition:all .2s;">
                            <i class="bi bi-plus-circle"></i> Create an Event
                        </a>
                        @endguest
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>{{-- /ep-hero-wrap --}}

<div class="ep-flash" id="epFlash">
    @if(session('success'))
        <div class="ep-alert ep-alert-success">
            <i class="bi bi-check-circle-fill ep-alert-icon"></i>
            <span>{{ session('success') }}</span>
            <button class="ep-alert-close" onclick="this.closest('.ep-alert').remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="ep-alert ep-alert-danger">
            <i class="bi bi-exclamation-circle-fill ep-alert-icon"></i>
            <span>{{ session('error') }}</span>
            <button class="ep-alert-close" onclick="this.closest('.ep-alert').remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif
    @if(session('warning'))
        <div class="ep-alert ep-alert-warning">
            <i class="bi bi-exclamation-triangle-fill ep-alert-icon"></i>
            <span>{{ session('warning') }}</span>
            <button class="ep-alert-close" onclick="this.closest('.ep-alert').remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif
</div>

<main>@yield('content')</main>

<footer class="ep-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <a class="ep-footer-brand" href="{{ url('/') }}">
                    <span class="ep-footer-brand-icon"><i class="bi bi-calendar-event-fill"></i></span>
                    EventPro
                </a>
                <p>The all-in-one platform to create, manage, and attend amazing events. From intimate workshops to large concerts.</p>
                <div class="ep-footer-social">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="ep-footer-heading">Explore</div>
                <ul class="ep-footer-links">
                    <li><a href="{{ route('events.index') }}">Browse Events</a></li>
                    <li><a href="#">Categories</a></li>
                    <li><a href="#">Venues</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <div class="ep-footer-heading">Organizers</div>
                <ul class="ep-footer-links">
                    <li><a href="{{ route('register') }}">Create Event</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Help Center</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="ep-footer-heading">Stay Updated</div>
                <p>Get notified about upcoming events in your area.</p>
                <div class="input-group ep-footer-nl">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button class="btn" type="button">Subscribe</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="ep-footer-bottom">
            <span>&copy; {{ date('Y') }} EventPro. All rights reserved.</span>
            <div style="display:flex;gap:20px;">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    const nav = document.getElementById('epNav');
    @if(!View::hasSection('hide_hero'))
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });
    @endif

    document.querySelectorAll('.ep-alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
</script>
@stack('scripts')
</body>
</html>