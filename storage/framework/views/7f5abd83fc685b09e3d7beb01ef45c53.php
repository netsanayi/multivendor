<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?> - <?php echo $__env->yieldContent('title', 'Multi-Vendor Marketplace'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/html/dist/assets/media/app/favicon.ico')); ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/html/dist/assets/css/styles.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/html/dist/vendors/keenicons/styles.bundle.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/html/dist/vendors/apexcharts/apexcharts.css')); ?>">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338CA;
            --secondary-color: #7C3AED;
            --success-color: #10B981;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #3B82F6;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .main-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-menu-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 4px 12px;
        }
        
        .sidebar-menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar-menu-item.active {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        
        /* Card Styles */
        .custom-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        
        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #f93b1d, #ff6b6b);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(249, 59, 29, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(249, 59, 29, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(249, 59, 29, 0);
            }
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.1), rgba(124, 58, 237, 0.1));
            padding-left: 25px;
        }
        
        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--primary-hover), var(--secondary-color));
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <header class="header bg-white shadow-lg sticky top-0 z-50">
            <nav class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Left Section -->
                    <div class="flex items-center space-x-8">
                        <!-- Mobile Menu Toggle -->
                        <button id="mobile-menu-toggle" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <i class="ki-duotone ki-menu fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </button>
                        
                        <!-- Logo -->
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-3">
                            <img src="<?php echo e(asset('assets/html/dist/assets/media/app/default-logo.svg')); ?>" alt="Logo" class="h-10 w-auto">
                            <span class="text-xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent hidden md:block">
                                <?php echo e(config('app.name', 'MarketPlace')); ?>

                            </span>
                        </a>
                        
                        <!-- Desktop Navigation -->
                        <nav class="hidden lg:flex items-center space-x-6">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Ana Sayfa
                            </a>
                            <a href="#" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Ürünler
                            </a>
                            <a href="#" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Kategoriler
                            </a>
                            <?php if(auth()->user() && auth()->user()->hasRole('vendor')): ?>
                            <a href="<?php echo e(route('vendor.dashboard')); ?>" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Satıcı Paneli
                            </a>
                            <?php endif; ?>
                            <?php if(auth()->user() && auth()->user()->hasRole('admin')): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-gray-700 hover:text-purple-600 font-medium transition-colors">
                                Yönetim
                            </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    
                    <!-- Right Section -->
                    <div class="flex items-center space-x-4">
                        <!-- Search Bar -->
                        <div class="hidden md:block">
                            <div class="relative">
                                <input type="text" placeholder="Ürün ara..." class="w-64 px-4 py-2 pr-10 text-sm bg-gray-100 border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="ki-duotone ki-magnifier fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="text-gray-600 hover:text-gray-900 relative">
                                <i class="ki-duotone ki-notification fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="notification-badge">3</span>
                            </button>
                        </div>
                        
                        <!-- Messages -->
                        <div class="relative">
                            <a href="<?php echo e(route('messages.index')); ?>" class="text-gray-600 hover:text-gray-900 relative">
                                <i class="ki-duotone ki-message-text fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <span class="notification-badge">5</span>
                            </a>
                        </div>
                        
                        <!-- Wishlist -->
                        <a href="<?php echo e(route('wishlist.index')); ?>" class="text-gray-600 hover:text-gray-900">
                            <i class="ki-duotone ki-heart fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900">
                                <img src="<?php echo e(asset('assets/html/dist/assets/media/avatars/300-1.png')); ?>" alt="User" class="w-10 h-10 rounded-full border-2 border-purple-500">
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold"><?php echo e(Auth::user()->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(Auth::user()->email); ?></p>
                                </div>
                                <i class="ki-duotone ki-down text-gray-400">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 dropdown-menu">
                                <div class="py-2">
                                    <a href="<?php echo e(route('profile.show')); ?>" class="dropdown-item flex items-center space-x-3">
                                        <i class="ki-duotone ki-user text-gray-500">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>Profilim</span>
                                    </a>
                                    <a href="#" class="dropdown-item flex items-center space-x-3">
                                        <i class="ki-duotone ki-setting-2 text-gray-500">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>Ayarlar</span>
                                    </a>
                                    <a href="<?php echo e(route('wishlist.index')); ?>" class="dropdown-item flex items-center space-x-3">
                                        <i class="ki-duotone ki-heart text-gray-500">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span>İstek Listem</span>
                                    </a>
                                    <hr class="my-2">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item flex items-center space-x-3 w-full text-left text-red-600">
                                            <i class="ki-duotone ki-exit-right">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span>Çıkış Yap</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="fixed inset-0 z-40 lg:hidden hidden">
            <div class="fixed inset-0 bg-black opacity-50" id="sidebar-overlay"></div>
            <nav class="fixed top-0 left-0 bottom-0 w-64 bg-white shadow-xl overflow-y-auto">
                <div class="p-4">
                    <button id="close-sidebar" class="text-gray-500 hover:text-gray-700 float-right">
                        <i class="ki-duotone ki-cross fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                    <img src="<?php echo e(asset('assets/html/dist/assets/media/app/default-logo.svg')); ?>" alt="Logo" class="h-10 w-auto mb-6">
                    
                    <div class="space-y-2">
                        <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                            <i class="ki-duotone ki-home-2 fs-5 mr-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ana Sayfa
                        </a>
                        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                            <i class="ki-duotone ki-shop fs-5 mr-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Ürünler
                        </a>
                        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                            <i class="ki-duotone ki-category fs-5 mr-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Kategoriler
                        </a>
                        <?php if(auth()->user() && auth()->user()->hasRole('vendor')): ?>
                        <a href="<?php echo e(route('vendor.dashboard')); ?>" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                            <i class="ki-duotone ki-briefcase fs-5 mr-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Satıcı Paneli
                        </a>
                        <?php endif; ?>
                        <?php if(auth()->user() && auth()->user()->hasRole('admin')): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                            <i class="ki-duotone ki-shield-tick fs-5 mr-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Yönetim Paneli
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <main class="min-h-screen">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-auto">
            <div class="container mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <img src="<?php echo e(asset('assets/html/dist/assets/media/app/default-logo-dark.svg')); ?>" alt="Logo" class="h-10 w-auto mb-4">
                        <p class="text-gray-400">Multi-Vendor Marketplace platformu ile güvenli alışveriş deneyimi.</p>
                        <div class="flex space-x-4 mt-4">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="ki-duotone ki-facebook fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="ki-duotone ki-twitter fs-2x">
                                    <span class="path1"></span>
                                </i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="ki-duotone ki-instagram fs-2x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Hızlı Linkler</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Hakkımızda</a></li>
                            <li><a href="#" class="hover:text-white">İletişim</a></li>
                            <li><a href="#" class="hover:text-white">Blog</a></li>
                            <li><a href="#" class="hover:text-white">Kariyer</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Müşteri Hizmetleri</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white">Yardım Merkezi</a></li>
                            <li><a href="#" class="hover:text-white">İade ve Değişim</a></li>
                            <li><a href="#" class="hover:text-white">Kargo Takibi</a></li>
                            <li><a href="#" class="hover:text-white">Güvenli Alışveriş</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Bülten</h4>
                        <p class="text-gray-400 mb-4">En güncel kampanyalardan haberdar olun!</p>
                        <div class="flex">
                            <input type="email" placeholder="E-posta adresiniz" class="bg-gray-800 text-white px-4 py-2 rounded-l-lg flex-1 focus:outline-none">
                            <button class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-r-lg transition-colors">
                                <i class="ki-duotone ki-arrow-right fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr class="border-gray-800 my-8">
                
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400">&copy; 2024 <?php echo e(config('app.name')); ?>. Tüm hakları saklıdır.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white">Gizlilik Politikası</a>
                        <a href="#" class="text-gray-400 hover:text-white">Kullanım Koşulları</a>
                        <a href="#" class="text-gray-400 hover:text-white">Çerez Politikası</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="<?php echo e(asset('assets/html/dist/vendors/ktui/ktui.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/html/dist/vendors/clipboard/clipboard.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/html/dist/vendors/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/html/dist/assets/js/core.bundle.js')); ?>"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.remove('hidden');
        });
        
        document.getElementById('close-sidebar').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('hidden');
        });
        
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('hidden');
        });
    </script>
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/layouts/app.blade.php ENDPATH**/ ?>