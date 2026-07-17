<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin Panel') - JDIH Kota Kendari</title>
    
    <!-- Bootstrap 5 untuk admin -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE 3 (opsional jika pakai) -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"> -->
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 2px;
        }
        .sidebar-menu a {
            color: rgba(255,255,255,0.8);
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 3px solid white;
        }
        .sidebar-menu a.active {
            color: white;
            background: rgba(255,255,255,0.15);
            border-left: 3px solid white;
        }
        .sidebar-menu i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }
        .navbar-admin {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-left: var(--sidebar-width);
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        .content-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar span:not(.fa) {
                display: none;
            }
            .sidebar-header h4 {
                display: none;
            }
            .main-content {
                margin-left: 70px;
            }
            .navbar-admin {
                margin-left: 70px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="text-center mb-0">
                <i class="fas fa-balance-scale"></i>
                <span>JDIH Admin</span>
            </h4>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('backend.dashboard') }}" class="{{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('backend.disabilitas.index') }}" class="{{ request()->routeIs('backend.disabilitas.*') ? 'active' : '' }}">
                    <i class="fas fa-wheelchair"></i>
                    <span>Disabilitas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('backend.peraturan.index') }}" class="{{ request()->routeIs('backend.peraturan.*') ? 'active' : '' }}">
                    <i class="fas fa-gavel"></i>
                    <span>Peraturan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('backend.informasi-hukum.index') }}" class="{{ request()->routeIs('backend.informasi-hukum.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Informasi Hukum</span>
                </a>
            </li>
            <li>
                <a href="{{ route('backend.berita.index') }}" class="{{ request()->routeIs('backend.berita.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span>Berita</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <nav class="navbar-admin">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                    @if(isset($listNav) && is_array($listNav))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                @foreach($listNav as $nav)
                                    @if($loop->last)
                                        <li class="breadcrumb-item active">{{ $nav['label'] }}</li>
                                    @else
                                        <li class="breadcrumb-item"><a href="{{ $nav['route'] }}">{{ $nav['label'] }}</a></li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                </div>
                <div>
                    <span class="text-muted">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ auth()->user()->username ?? 'Admin' }}
                    </span>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>