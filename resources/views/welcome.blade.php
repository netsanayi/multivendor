<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} - Çok Satıcılı Pazaryeri</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Navigation -->
            <nav class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="/">
                                    <x-application-mark class="block h-9 w-auto" />
                                </a>
                            </div>
                        </div>

                        <!-- Login/Register Links -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            @if (Route::has('login'))
                                <div class="space-x-4">
                                    @auth
                                        @if(auth()->user()->hasRole('admin'))
                                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                                Admin Panel
                                            </a>
                                        @elseif(auth()->user()->hasRole('vendor'))
                                            <a href="{{ route('vendor.dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                                Satıcı Panel
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                                Hesabım
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                            Giriş Yap
                                        </a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium">
                                                Kayıt Ol
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto">
                    <div class="relative z-10 pb-8 bg-white dark:bg-gray-800 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                        <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                            <div class="sm:text-center lg:text-left">
                                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                                    <span class="block xl:inline">Çok Satıcılı</span>{' '}
                                    <span class="block text-indigo-600 xl:inline">Pazaryeri</span>
                                </h1>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                    Binlerce satıcı, milyonlarca ürün. En iyi fiyatlar ve güvenli alışveriş deneyimi için doğru adrestesiniz.
                                </p>
                                <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                    <div class="rounded-md shadow">
                                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                            Hemen Başla
                                        </a>
                                    </div>
                                    <div class="mt-3 sm:mt-0 sm:ml-3">
                                        <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                            Daha Fazla Bilgi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
                <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                    <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" alt="E-commerce">
                </div>
            </div>

            <!-- Features Section -->
            <div id="features" class="py-12 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:text-center">
                        <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Özellikler</h2>
                        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                            Neden Bizi Tercih Etmelisiniz?
                        </p>
                        <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                            Modern altyapı ve kullanıcı dostu arayüz ile e-ticaret deneyimini yeniden tanımlıyoruz.
                        </p>
                    </div>

                    <div class="mt-10">
                        <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                            <div class="relative">
                                <dt>
                                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Hızlı ve Güvenli</p>
                                </dt>
                                <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                    En son teknolojiler kullanılarak geliştirilmiş güvenli altyapı ile hızlı ve kesintisiz alışveriş deneyimi.
                                </dd>
                            </div>

                            <div class="relative">
                                <dt>
                                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                        </svg>
                                    </div>
                                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Adil Komisyon</p>
                                </dt>
                                <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                    Satıcılarımız için en düşük komisyon oranları ve şeffaf fiyatlandırma politikası.
                                </dd>
                            </div>

                            <div class="relative">
                                <dt>
                                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                    </div>
                                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">7/24 Destek</p>
                                </dt>
                                <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                    Profesyonel destek ekibimiz her zaman yanınızda. Sorularınız için 7/24 ulaşabilirsiniz.
                                </dd>
                            </div>

                            <div class="relative">
                                <dt>
                                    <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </div>
                                    <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Kolay İade</p>
                                </dt>
                                <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                    14 gün içinde koşulsuz iade garantisi ile güvenli alışveriş imkanı.
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-indigo-700">
                <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                        <span class="block">Satıcı olmaya hazır mısınız?</span>
                        <span class="block">Hemen başlayın.</span>
                    </h2>
                    <p class="mt-4 text-lg leading-6 text-indigo-200">
                        Binlerce müşteriye ulaşın, satışlarınızı artırın. Ücretsiz kayıt olun ve satmaya başlayın.
                    </p>
                    <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 sm:w-auto">
                        Satıcı Olarak Kayıt Ol
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-gray-800" aria-labelledby="footer-heading">
                <h2 id="footer-heading" class="sr-only">Footer</h2>
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
                    <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                        <div class="space-y-8 xl:col-span-1">
                            <x-application-mark class="h-10 w-auto text-gray-300" />
                            <p class="text-gray-400 text-base">
                                Türkiye'nin en büyük çok satıcılı pazaryeri platformu.
                            </p>
                        </div>
                        <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                            <div class="md:grid md:grid-cols-2 md:gap-8">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">
                                        Kurumsal
                                    </h3>
                                    <ul class="mt-4 space-y-4">
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Hakkımızda
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Blog
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-12 md:mt-0">
                                    <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">
                                        Destek
                                    </h3>
                                    <ul class="mt-4 space-y-4">
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Yardım Merkezi
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                İletişim
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="md:grid md:grid-cols-2 md:gap-8">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">
                                        Yasal
                                    </h3>
                                    <ul class="mt-4 space-y-4">
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Gizlilik Politikası
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Kullanım Şartları
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mt-12 md:mt-0">
                                    <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">
                                        Satıcı
                                    </h3>
                                    <ul class="mt-4 space-y-4">
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Satıcı Ol
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="text-base text-gray-400 hover:text-white">
                                                Satıcı Paneli
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 border-t border-gray-700 pt-8">
                        <p class="text-base text-gray-400 xl:text-center">
                            &copy; 2025 {{ config('app.name', 'Laravel') }}. Tüm hakları saklıdır.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
