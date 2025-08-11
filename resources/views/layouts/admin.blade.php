<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin Panel - @yield('title', 'Dashboard')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/html/dist/assets/media/app/favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/html/dist/assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/html/dist/vendors/keenicons/styles.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/html/dist/vendors/apexcharts/apexcharts.css') }}">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f8fa;
        }
        
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed {
            width: 80px;
        }
        
        .admin-header {
            height: var(--header-height);
            background: white;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed ~ .admin-header {
            left: 80px;
        }
        
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 30px;
            min-height: calc(100vh - var(--header-height));
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed ~ .admin-content {
            margin-left: 80px;
        }
        
        .sidebar-menu-item {
            padding: 12px 20px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 4px 15px;
            position: relative;
            text-decoration: none;
        }
        
        .sidebar-menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-menu-item.active {
            background: linear-gradient(90deg, #7c3aed, #2563eb);
            color: white;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }
        
        .sidebar-menu-item.active::before {
            content: '';
            position: absolute;
            left: -15px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 70%;
            background: white;
            border-radius: 0 4px 4px 0;
        }
        
        .sidebar-dropdown {
            display: none;
            padding-left: 50px;
        }
        
        .sidebar-dropdown.open {
            display: block;
        }
        
        .sidebar-dropdown-item {
            display: block;
            padding: 8px 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .sidebar-dropdown-item:hover {
            color: white;
            padding-left: 25px;
        }
        
        .admin-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 24px;
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50px;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(37, 99, 235, 0.1));
            border-radius: 50%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    @stack('styles')
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/html/dist/assets/media/app/mini-logo-circle-primary.svg') }}" alt="Logo" class="w-10 h-10">
                    <span class="text-white font-bold text-xl sidebar-text">Admin Panel</span>
                </div>
                <button id="sidebarToggle" class="text-gray-400 hover:text-white">
                    <i class="ki-duotone ki-abstract-14 fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
        </div>
        
        <nav class="py-4">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ki-duotone ki-element-11 fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            
            <!-- E-Commerce Menu -->
            <div class="mt-6 mb-2 px-6">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider sidebar-text">E-Commerce</p>
            </div>
            
            <!-- Products -->
            <div>
                <button class="sidebar-menu-item w-full text-left dropdown-toggle" data-dropdown="products">
                    <i class="ki-duotone ki-package fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <span class="sidebar-text flex-1">Ürünler</span>
                    <i class="ki-duotone ki-down fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
                <div class="sidebar-dropdown" id="products-dropdown">
                    <a href="{{ route('admin.products.index') }}" class="sidebar-dropdown-item">Tüm Ürünler</a>
                    <a href="{{ route('admin.products.create') }}" class="sidebar-dropdown-item">Yeni Ürün</a>
                    <a href="{{ route('admin.categories.index') }}" class="sidebar-dropdown-item">Kategoriler</a>
                    <a href="{{ route('admin.brands.index') }}" class="sidebar-dropdown-item">Markalar</a>
                    <a href="{{ route('admin.product-attributes.index') }}" class="sidebar-dropdown-item">Ürün Özellikleri</a>
                    <a href="{{ route('admin.attribute-categories.index') }}" class="sidebar-dropdown-item">Özellik Kategorileri</a>
                </div>
            </div>
            
            <!-- Orders -->
            <a href="{{ route('admin.orders.index') ?? '#' }}" class="sidebar-menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-basket fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <span class="sidebar-text">Siparişler</span>
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">5</span>
            </a>
            
            <!-- Vendor Products -->
            <a href="{{ route('admin.vendor-products.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.vendor-products.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-shop fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <span class="sidebar-text">Satıcı Ürünleri</span>
            </a>
            
            <!-- Wishlists -->
            <a href="{{ route('wishlist.index') }}" class="sidebar-menu-item {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-heart fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Favoriler</span>
            </a>
            
            <!-- Users Menu -->
            <div class="mt-6 mb-2 px-6">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider sidebar-text">Kullanıcılar</p>
            </div>
            
            <!-- Users -->
            <div>
                <button class="sidebar-menu-item w-full text-left dropdown-toggle" data-dropdown="users">
                    <i class="ki-duotone ki-people fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    <span class="sidebar-text flex-1">Kullanıcılar</span>
                    <i class="ki-duotone ki-down fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
                <div class="sidebar-dropdown" id="users-dropdown">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-dropdown-item">Tüm Kullanıcılar</a>
                    <a href="{{ route('admin.users.create') }}" class="sidebar-dropdown-item">Yeni Kullanıcı</a>
                    <a href="{{ route('admin.roles.index') }}" class="sidebar-dropdown-item">Roller</a>
                    <a href="{{ route('admin.vendors.list') ?? '#' }}" class="sidebar-dropdown-item">Satıcılar</a>
                    <a href="{{ route('admin.addresses.index') }}" class="sidebar-dropdown-item">Adresler</a>
                </div>
            </div>
            
            <!-- Vendor Dashboard -->
            <a href="{{ route('vendor.dashboard') ?? '#' }}" class="sidebar-menu-item {{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-shop fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <span class="sidebar-text">Satıcı Paneli</span>
            </a>
            
            <!-- Content Menu -->
            <div class="mt-6 mb-2 px-6">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider sidebar-text">İçerik</p>
            </div>
            
            <!-- Blog -->
            <a href="{{ route('admin.blogs.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-document fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Blog</span>
            </a>
            
            <!-- Banners -->
            <a href="{{ route('admin.banners.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-picture fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Bannerlar</span>
            </a>
            
            <!-- Support Menu -->
            <div class="mt-6 mb-2 px-6">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider sidebar-text">Destek</p>
            </div>
            
            <!-- Tickets -->
            <a href="{{ route('tickets.index') ?? '#' }}" class="sidebar-menu-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-support-24 fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <span class="sidebar-text">Destek Talepleri</span>
            </a>
            
            <!-- Messages -->
            <a href="{{ route('messages.index') ?? '#' }}" class="sidebar-menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-message-text fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <span class="sidebar-text">Mesajlar</span>
            </a>
            
            <!-- Notifications -->
            <a href="{{ route('notifications.index') ?? '#' }}" class="sidebar-menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-notification fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Bildirimler</span>
            </a>
            
            <!-- Settings Menu -->
            <div class="mt-6 mb-2 px-6">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider sidebar-text">Ayarlar</p>
            </div>
            
            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-setting-2 fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Genel Ayarlar</span>
            </a>
            
            <!-- Languages -->
            <a href="{{ route('admin.languages.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-flag fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Diller</span>
            </a>
            
            <!-- Currencies -->
            <a href="{{ route('admin.currencies.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.currencies.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-dollar fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <span class="sidebar-text">Para Birimleri</span>
            </a>
            
            <!-- Activity Log -->
            <a href="{{ route('admin.activity-log.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                <i class="ki-duotone ki-time fs-2x">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="sidebar-text">Aktivite Logları</span>
            </a>
        </nav>
    </aside>
    
    <!-- Header -->
    <header class="admin-header">
        <div class="h-full px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                @yield('breadcrumb')
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="Ara..." class="w-64 px-4 py-2 pr-10 bg-gray-100 border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="ki-duotone ki-magnifier fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>
                
                <!-- Notifications -->
                <button class="relative text-gray-600 hover:text-gray-900">
                    <i class="ki-duotone ki-notification fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                </button>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3">
                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/html/dist/assets/media/avatars/300-1.png') }}" alt="User" class="w-10 h-10 rounded-full">
                        <div class="text-left">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">
                                @if(Auth::user()->hasRole('super-admin'))
                                    Super Admin
                                @elseif(Auth::user()->hasRole('admin'))
                                    Admin
                                @elseif(Auth::user()->hasRole('vendor'))
                                    Satıcı
                                @else
                                    Kullanıcı
                                @endif
                            </p>
                        </div>
                        <i class="ki-duotone ki-down text-gray-400">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
                        <div class="py-2">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="ki-duotone ki-user fs-5 mr-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Profilim
                            </a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="ki-duotone ki-home-2 fs-5 mr-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Ana Sayfa
                            </a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="ki-duotone ki-exit-right fs-5 mr-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="admin-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Scripts -->
    <script src="{{ asset('assets/html/dist/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/html/dist/vendors/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/html/dist/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/html/dist/assets/js/core.bundle.js') }}"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('collapsed');
            
            // Hide text when collapsed
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            sidebarTexts.forEach(text => {
                if (document.getElementById('adminSidebar').classList.contains('collapsed')) {
                    text.style.display = 'none';
                } else {
                    setTimeout(() => {
                        text.style.display = 'inline';
                    }, 200);
                }
            });
        });
        
        // Dropdown Toggle
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const dropdownId = this.getAttribute('data-dropdown');
                const dropdown = document.getElementById(dropdownId + '-dropdown');
                dropdown.classList.toggle('open');
                
                // Rotate arrow icon
                const arrow = this.querySelector('.ki-down');
                if (dropdown.classList.contains('open')) {
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });
    </script>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
