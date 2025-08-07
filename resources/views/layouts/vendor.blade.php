<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vendor Dashboard') - {{ config('app.name', 'Laravel Multi-Vendor') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #c2c7d0;
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .border-left-primary {
            border-left: 4px solid #007bff;
        }
        .border-left-success {
            border-left: 4px solid #28a745;
        }
        .border-left-warning {
            border-left: 4px solid #ffc107;
        }
        .border-left-info {
            border-left: 4px solid #17a2b8;
        }
        .border-left-danger {
            border-left: 4px solid #dc3545;
        }
    </style>

    @stack('styles')
    @livewireStyles
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="text-center mb-4">
                    <h5 class="text-white">Vendor Panel</h5>
                    <p class="text-muted small">{{ auth()->user()->name }}</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" 
                           href="{{ route('vendor.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}" 
                           href="{{ route('vendor.products.index') }}">
                            <i class="fas fa-box"></i> Ürünler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.orders') ? 'active' : '' }}" 
                           href="{{ route('vendor.orders') }}">
                            <i class="fas fa-shopping-cart"></i> Siparişler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.earnings') ? 'active' : '' }}" 
                           href="{{ route('vendor.earnings') }}">
                            <i class="fas fa-dollar-sign"></i> Kazançlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.payouts') ? 'active' : '' }}" 
                           href="{{ route('vendor.payouts') }}">
                            <i class="fas fa-money-check-alt"></i> Ödemeler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.analytics') ? 'active' : '' }}" 
                           href="{{ route('vendor.analytics') }}">
                            <i class="fas fa-chart-line"></i> Analizler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendor.settings') ? 'active' : '' }}" 
                           href="{{ route('vendor.settings') }}">
                            <i class="fas fa-cog"></i> Ayarlar
                        </a>
                    </li>
                    
                    <hr class="my-3 bg-secondary">
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ml-sm-auto px-0">
                <!-- Top Navbar -->
                <div class="top-navbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
                        <div>
                            <span class="badge bg-info">
                                <i class="fas fa-clock"></i> {{ now()->format('d.m.Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mx-3" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="main-content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
