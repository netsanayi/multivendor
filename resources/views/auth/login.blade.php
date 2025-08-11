<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Giriş Yap</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            color: #374151;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite ease-in-out;
        }
        
        .shape1 {
            width: 80px;
            height: 80px;
            background: white;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape2 {
            width: 120px;
            height: 120px;
            background: white;
            top: 60%;
            left: 80%;
            animation-delay: 5s;
        }
        
        .shape3 {
            width: 60px;
            height: 60px;
            background: white;
            top: 80%;
            left: 20%;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            33% {
                transform: translateY(-20px) rotate(120deg);
            }
            66% {
                transform: translateY(20px) rotate(240deg);
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
    </div>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Left Side - Form -->
                <div class="p-8 lg:p-12">
                    <div class="mb-8">
                        <a href="/" class="inline-flex items-center gap-3 mb-6">
                            <img src="{{ asset('assets/html/dist/assets/media/app/default-logo.svg') }}" alt="Logo" class="h-10">
                            <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                {{ config('app.name', 'MarketPlace') }}
                            </span>
                        </a>
                        
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Hoş Geldiniz! 👋</h2>
                        <p class="text-gray-600">Hesabınıza giriş yaparak alışverişe devam edin</p>
                    </div>
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email -->
                        <div class="mb-5">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="form-input pl-12">
                                <i class="ki-duotone ki-sms absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-5">
                            <label for="password" class="form-label">Şifre</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required class="form-input pl-12">
                                <i class="ki-duotone ki-lock-2 absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Beni Hatırla</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                    Şifremi Unuttum?
                                </a>
                            @endif
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn-primary mb-6">
                            <i class="ki-duotone ki-entrance-right fs-5 mr-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Giriş Yap
                        </button>
                        
                        <!-- Social Login -->
                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">veya</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <a href="#" class="social-btn">
                                <img src="{{ asset('assets/html/dist/assets/media/brand-logos/google.svg') }}" alt="Google" class="w-5 h-5">
                                <span>Google</span>
                            </a>
                            <a href="#" class="social-btn">
                                <img src="{{ asset('assets/html/dist/assets/media/brand-logos/facebook.svg') }}" alt="Facebook" class="w-5 h-5">
                                <span>Facebook</span>
                            </a>
                        </div>
                        
                        <!-- Sign Up Link -->
                        <p class="text-center text-gray-600">
                            Hesabınız yok mu? 
                            <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 font-semibold">
                                Üye Ol
                            </a>
                        </p>
                    </form>
                </div>
                
                <!-- Right Side - Image/Info -->
                <div class="hidden lg:flex items-center justify-center p-12 bg-gradient-to-br from-purple-600 to-blue-600">
                    <div class="text-center text-white">
                        <img src="{{ asset('assets/html/dist/assets/media/illustrations/18.svg') }}" alt="Login" class="w-full max-w-md mx-auto mb-8">
                        <h3 class="text-2xl font-bold mb-4">Binlerce Ürün, Tek Adres</h3>
                        <p class="text-purple-100 mb-8">
                            En iyi markaları, en uygun fiyatlarla keşfedin. Güvenli alışverişin tadını çıkarın.
                        </p>
                        
                        <div class="flex items-center justify-center gap-8 text-white">
                            <div>
                                <div class="text-3xl font-bold">10K+</div>
                                <div class="text-purple-200 text-sm">Satıcı</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold">50K+</div>
                                <div class="text-purple-200 text-sm">Ürün</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold">100K+</div>
                                <div class="text-purple-200 text-sm">Müşteri</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>