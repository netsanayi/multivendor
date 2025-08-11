<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Multi-Vendor Marketplace</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/html/dist/assets/media/app/favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/html/dist/assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/html/dist/vendors/keenicons/styles.bundle.css') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Header -->
    <header class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <a href="/" class="flex items-center space-x-3">
                        <img src="{{ asset('assets/html/dist/assets/media/app/default-logo.svg') }}" alt="Logo" class="h-10 w-auto">
                        <span class="text-2xl font-bold gradient-text">MarketPlace</span>
                    </a>
                    
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="#features" class="text-gray-600 hover:text-purple-600 font-medium transition-colors">Özellikler</a>
                        <a href="#categories" class="text-gray-600 hover:text-purple-600 font-medium transition-colors">Kategoriler</a>
                        <a href="#vendors" class="text-gray-600 hover:text-purple-600 font-medium transition-colors">Satıcılar</a>
                        <a href="#contact" class="text-gray-600 hover:text-purple-600 font-medium transition-colors">İletişim</a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-purple-600 font-medium">Giriş Yap</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all">
                                    Üye Ol
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="hero-pattern pt-24 pb-20 min-h-screen flex items-center">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl lg:text-6xl font-bold text-gray-800 mb-6">
                        En İyi <span class="gradient-text">Alışveriş</span> Deneyimi
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Binlerce satıcı, milyonlarca ürün. Güvenli alışverişin adresi. Hemen keşfetmeye başlayın!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:shadow-xl transition-all inline-block text-center">
                            Hemen Başla
                        </a>
                        <a href="#features" class="bg-white border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-purple-50 transition-all inline-block text-center">
                            Daha Fazla Bilgi
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-8 mt-12">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-800">10K+</h3>
                            <p class="text-gray-600">Aktif Satıcı</p>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-800">50K+</h3>
                            <p class="text-gray-600">Ürün</p>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-800">100K+</h3>
                            <p class="text-gray-600">Mutlu Müşteri</p>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <img src="{{ asset('assets/html/dist/assets/media/illustrations/1.svg') }}" alt="Hero" class="w-full floating">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Neden Bizi Tercih Etmelisiniz?</h2>
                <p class="text-xl text-gray-600">Güvenli ve kolay alışverişin tüm avantajları</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-2xl p-8 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ki-duotone ki-shield-tick text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Güvenli Ödeme</h3>
                    <p class="text-gray-600">256-bit SSL şifreleme ile güvenli ödeme altyapısı</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ki-duotone ki-delivery text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Hızlı Teslimat</h3>
                    <p class="text-gray-600">Siparişleriniz en kısa sürede kapınızda</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ki-duotone ki-like-shapes text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Kalite Garantisi</h3>
                    <p class="text-gray-600">Tüm ürünler kalite kontrolünden geçer</p>
                </div>
                
                <div class="bg-white rounded-2xl p-8 text-center card-hover">
                    <div class="w-20 h-20 bg-gradient-to-r from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ki-duotone ki-support-24 text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">7/24 Destek</h3>
                    <p class="text-gray-600">Her zaman yanınızdayız</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section id="categories" class="py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Popüler Kategoriler</h2>
                <p class="text-xl text-gray-600">Binlerce ürün arasından seçim yapın</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-200 transition-colors">
                            <i class="ki-duotone ki-laptop text-purple-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Elektronik</h4>
                    </div>
                </a>
                
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                            <i class="ki-duotone ki-t-shirt text-blue-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Giyim</h4>
                    </div>
                </a>
                
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition-colors">
                            <i class="ki-duotone ki-home text-green-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Ev & Yaşam</h4>
                    </div>
                </a>
                
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors">
                            <i class="ki-duotone ki-book text-orange-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Kitap</h4>
                    </div>
                </a>
                
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-pink-200 transition-colors">
                            <i class="ki-duotone ki-heart text-pink-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Kozmetik</h4>
                    </div>
                </a>
                
                <a href="#" class="group">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-lg card-hover">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-indigo-200 transition-colors">
                            <i class="ki-duotone ki-baseball text-indigo-600 fs-2x">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <h4 class="font-semibold text-gray-800">Spor</h4>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Satıcı Olmak İster Misiniz?</h2>
            <p class="text-xl text-purple-100 mb-8">Binlerce müşteriye ulaşın, işinizi büyütün</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all inline-block">
                    <i class="ki-duotone ki-shop fs-5 mr-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Satıcı Ol
                </a>
                <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white hover:text-purple-600 transition-all inline-block">
                    Daha Fazla Bilgi
                </a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="{{ asset('assets/html/dist/assets/media/app/default-logo-dark.svg') }}" alt="Logo" class="h-10 w-auto mb-4">
                    <p class="text-gray-400">Güvenli alışverişin adresi</p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Kurumsal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Hakkımızda</a></li>
                        <li><a href="#" class="hover:text-white">İletişim</a></li>
                        <li><a href="#" class="hover:text-white">Kariyer</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Müşteri Hizmetleri</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Yardım Merkezi</a></li>
                        <li><a href="#" class="hover:text-white">İade Politikası</a></li>
                        <li><a href="#" class="hover:text-white">Kargo Bilgileri</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Bizi Takip Edin</h4>
                    <div class="flex space-x-4">
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
            </div>
            
            <hr class="border-gray-800 my-8">
            
            <div class="text-center text-gray-400">
                <p>&copy; 2024 {{ config('app.name') }}. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>