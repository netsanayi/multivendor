<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Üye Ol</title>
    
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
        
        .account-type-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .account-type-card:hover {
            border-color: #7c3aed;
            background: #f9f5ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(124, 58, 237, 0.1);
        }
        
        .account-type-card.selected {
            border-color: #7c3aed;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
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
        
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .progress-step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .progress-step::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: -1;
        }
        
        .progress-step:last-child::before {
            display: none;
        }
        
        .progress-step.active .progress-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .progress-step.active ~ .progress-step .progress-number {
            background: white;
            color: #9ca3af;
            border: 2px solid #e5e7eb;
        }
        
        .progress-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 600;
        }
        
        .progress-label {
            font-size: 14px;
            color: #6b7280;
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
                <!-- Left Side - Image/Info -->
                <div class="hidden lg:flex items-center justify-center p-12 bg-gradient-to-br from-purple-600 to-blue-600">
                    <div class="text-center text-white">
                        <img src="{{ asset('assets/html/dist/assets/media/illustrations/19.svg') }}" alt="Register" class="w-full max-w-md mx-auto mb-8">
                        <h3 class="text-2xl font-bold mb-4">Hemen Üye Olun!</h3>
                        <p class="text-purple-100 mb-8">
                            Özel kampanyalardan yararlanın, favori ürünlerinizi kaydedin ve daha fazlası...
                        </p>
                        
                        <div class="text-left bg-white/10 backdrop-blur rounded-lg p-6">
                            <h4 class="font-semibold mb-4">Üyelik Avantajları:</h4>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3">
                                    <i class="ki-duotone ki-check-circle text-green-300 fs-5 mt-0.5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-purple-100">Özel indirimler ve kampanyalar</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="ki-duotone ki-check-circle text-green-300 fs-5 mt-0.5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-purple-100">Hızlı ve güvenli ödeme</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="ki-duotone ki-check-circle text-green-300 fs-5 mt-0.5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-purple-100">Sipariş takibi ve geçmişi</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="ki-duotone ki-check-circle text-green-300 fs-5 mt-0.5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-purple-100">İstek listesi oluşturma</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Form -->
                <div class="p-8 lg:p-12">
                    <div class="mb-8">
                        <a href="/" class="inline-flex items-center gap-3 mb-6">
                            <img src="{{ asset('assets/html/dist/assets/media/app/default-logo.svg') }}" alt="Logo" class="h-10">
                            <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                {{ config('app.name', 'MarketPlace') }}
                            </span>
                        </a>
                        
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Hesap Oluştur 🚀</h2>
                        <p class="text-gray-600">Birkaç adımda üye olun ve alışverişe başlayın</p>
                    </div>
                    
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
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <!-- Account Type Selection -->
                        <div class="mb-6">
                            <label class="form-label">Hesap Türü</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="account-type-card selected" onclick="selectAccountType(this, 'customer')">
                                    <input type="radio" name="account_type" value="customer" checked hidden>
                                    <div class="text-center">
                                        <i class="ki-duotone ki-user fs-2x text-purple-600 mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <h4 class="font-semibold text-gray-800">Müşteri</h4>
                                        <p class="text-xs text-gray-500 mt-1">Alışveriş yapmak için</p>
                                    </div>
                                </div>
                                <div class="account-type-card" onclick="selectAccountType(this, 'vendor')">
                                    <input type="radio" name="account_type" value="vendor" hidden>
                                    <div class="text-center">
                                        <i class="ki-duotone ki-shop fs-2x text-purple-600 mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <h4 class="font-semibold text-gray-800">Satıcı</h4>
                                        <p class="text-xs text-gray-500 mt-1">Ürün satmak için</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Name Fields -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="form-label">Ad</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required class="form-input">
                            </div>
                            <div>
                                <label for="last_name" class="form-label">Soyad</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required class="form-input">
                            </div>
                        </div>
                        
                        <input type="hidden" name="name" id="full_name">
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input pl-12">
                                <i class="ki-duotone ki-sms absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-4">
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
                            <div class="mt-2">
                                <div class="flex gap-1">
                                    <div class="h-1 flex-1 bg-gray-200 rounded"></div>
                                    <div class="h-1 flex-1 bg-gray-200 rounded"></div>
                                    <div class="h-1 flex-1 bg-gray-200 rounded"></div>
                                    <div class="h-1 flex-1 bg-gray-200 rounded"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">En az 8 karakter, büyük/küçük harf ve rakam içermelidir</p>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Şifre Tekrar</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input pl-12">
                                <i class="ki-duotone ki-lock-2 absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Terms & Conditions -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" name="terms" required class="mt-1 rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">
                                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Kullanım Koşulları</a> ve 
                                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Gizlilik Politikası</a>'nı 
                                    okudum ve kabul ediyorum
                                </span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn-primary mb-6">
                            <i class="ki-duotone ki-user-tick fs-5 mr-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Üye Ol
                        </button>
                        
                        <!-- Sign In Link -->
                        <p class="text-center text-gray-600">
                            Zaten hesabınız var mı? 
                            <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold">
                                Giriş Yap
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function selectAccountType(element, type) {
            // Remove selected class from all cards
            document.querySelectorAll('.account-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            element.classList.add('selected');
            
            // Check the radio button
            element.querySelector('input[type="radio"]').checked = true;
        }
        
        // Combine first and last name
        document.getElementById('first_name').addEventListener('input', updateFullName);
        document.getElementById('last_name').addEventListener('input', updateFullName);
        
        function updateFullName() {
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            document.getElementById('full_name').value = firstName + ' ' + lastName;
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBars = document.querySelectorAll('.flex.gap-1 > div');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[@$!%*?&#]/.test(password)) strength++;
            
            strengthBars.forEach((bar, index) => {
                if (index < strength) {
                    bar.classList.remove('bg-gray-200');
                    if (strength === 1) bar.classList.add('bg-red-500');
                    else if (strength === 2) bar.classList.add('bg-yellow-500');
                    else if (strength === 3) bar.classList.add('bg-blue-500');
                    else bar.classList.add('bg-green-500');
                } else {
                    bar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500');
                    bar.classList.add('bg-gray-200');
                }
            });
        });
    </script>
</body>
</html>