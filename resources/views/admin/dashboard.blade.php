@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm text-gray-600">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Admin</a>
    <i class="ki-duotone ki-right fs-6 text-gray-400"></i>
    <span class="text-gray-900">Dashboard</span>
</nav>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Hoş Geldiniz, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-purple-100">Sistem yönetim panelinize hoş geldiniz. Bugün {{ now()->locale('tr')->isoFormat('DD MMMM YYYY, dddd') }}</p>
            </div>
            <div class="hidden lg:block">
                <img src="{{ asset('assets/html/dist/assets/media/illustrations/1.svg') }}" alt="" class="h-32">
            </div>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="ki-duotone ki-people text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </div>
                <span class="text-green-500 text-sm font-semibold bg-green-100 px-2 py-1 rounded">+12%</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">8,234</h3>
            <p class="text-gray-500 text-sm mt-1">Toplam Kullanıcı</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Bu ay</span>
                    <span class="font-semibold text-gray-700">+234</span>
                </div>
            </div>
        </div>
        
        <!-- Total Orders -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <i class="ki-duotone ki-basket text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </div>
                <span class="text-green-500 text-sm font-semibold bg-green-100 px-2 py-1 rounded">+8%</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">3,456</h3>
            <p class="text-gray-500 text-sm mt-1">Toplam Sipariş</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Bu ay</span>
                    <span class="font-semibold text-gray-700">+456</span>
                </div>
            </div>
        </div>
        
        <!-- Revenue -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="ki-duotone ki-dollar text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                <span class="text-green-500 text-sm font-semibold bg-green-100 px-2 py-1 rounded">+23%</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">₺234,567</h3>
            <p class="text-gray-500 text-sm mt-1">Toplam Gelir</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Bu ay</span>
                    <span class="font-semibold text-gray-700">₺45,678</span>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <i class="ki-duotone ki-package text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                <span class="text-red-500 text-sm font-semibold bg-red-100 px-2 py-1 rounded">-2%</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">1,234</h3>
            <p class="text-gray-500 text-sm mt-1">Aktif Ürün</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Stokta</span>
                    <span class="font-semibold text-gray-700">987</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Gelir Grafiği</h3>
                <select class="bg-gray-100 border-0 rounded-lg px-4 py-2 text-sm focus:outline-none">
                    <option>Bu Yıl</option>
                    <option>Son 6 Ay</option>
                    <option>Son 30 Gün</option>
                </select>
            </div>
            <div id="revenueChart" style="height: 350px;"></div>
        </div>
        
        <!-- Category Distribution -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Kategori Dağılımı</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <i class="ki-duotone ki-dots-horizontal fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </button>
            </div>
            <div id="categoryChart" style="height: 350px;"></div>
        </div>
    </div>
    
    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Son Siparişler</h3>
                <a href="#" class="text-purple-600 hover:text-purple-700 text-sm font-semibold">Tümünü Gör →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase">Sipariş</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase">Müşteri</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase">Tutar</th>
                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm">#2024-001</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('assets/html/dist/assets/media/avatars/300-2.png') }}" alt="" class="w-8 h-8 rounded-full">
                                    <span class="text-sm font-medium">Ahmet Y.</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-semibold">₺1,234</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded">Tamamlandı</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm">#2024-002</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('assets/html/dist/assets/media/avatars/300-3.png') }}" alt="" class="w-8 h-8 rounded-full">
                                    <span class="text-sm font-medium">Ayşe D.</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-semibold">₺856</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-600 bg-yellow-100 rounded">Beklemede</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm">#2024-003</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('assets/html/dist/assets/media/avatars/300-4.png') }}" alt="" class="w-8 h-8 rounded-full">
                                    <span class="text-sm font-medium">Mehmet K.</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-semibold">₺2,345</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded">Kargoda</span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 text-sm">#2024-004</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('assets/html/dist/assets/media/avatars/300-5.png') }}" alt="" class="w-8 h-8 rounded-full">
                                    <span class="text-sm font-medium">Fatma S.</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-semibold">₺678</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded">Tamamlandı</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Top Vendors -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">En İyi Satıcılar</h3>
                <a href="#" class="text-purple-600 hover:text-purple-700 text-sm font-semibold">Tümünü Gör →</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-6.png') }}" alt="" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-800">TechStore</p>
                            <p class="text-xs text-gray-500">234 ürün</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">₺45,678</p>
                        <p class="text-xs text-green-600">+12%</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-7.png') }}" alt="" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-800">FashionHub</p>
                            <p class="text-xs text-gray-500">156 ürün</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">₺34,567</p>
                        <p class="text-xs text-green-600">+8%</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-8.png') }}" alt="" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-800">HomeDecor</p>
                            <p class="text-xs text-gray-500">89 ürün</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">₺23,456</p>
                        <p class="text-xs text-red-600">-3%</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-9.png') }}" alt="" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-800">BookWorld</p>
                            <p class="text-xs text-gray-500">342 ürün</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">₺19,234</p>
                        <p class="text-xs text-green-600">+15%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Timeline -->
    <div class="admin-card">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Son Aktiviteler</h3>
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="relative">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="ki-duotone ki-user-tick text-purple-600 fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <div class="absolute top-10 left-5 w-0.5 h-16 bg-gray-200"></div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><span class="font-semibold">Yeni kullanıcı kaydı:</span> mehmet.kaya@email.com</p>
                    <p class="text-xs text-gray-500 mt-1">5 dakika önce</p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="relative">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ki-duotone ki-basket-ok text-green-600 fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                        </i>
                    </div>
                    <div class="absolute top-10 left-5 w-0.5 h-16 bg-gray-200"></div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><span class="font-semibold">Sipariş tamamlandı:</span> #2024-005 - ₺3,456</p>
                    <p class="text-xs text-gray-500 mt-1">15 dakika önce</p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="relative">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="ki-duotone ki-package text-blue-600 fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><span class="font-semibold">Yeni ürün eklendi:</span> Kablosuz Kulaklık Pro Max</p>
                    <p class="text-xs text-gray-500 mt-1">1 saat önce</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Revenue Chart
    var revenueOptions = {
        series: [{
            name: 'Gelir',
            data: [44000, 55000, 57000, 56000, 61000, 58000, 63000, 60000, 66000, 69000, 72000, 75000]
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        colors: ['#7C3AED'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "₺" + (val / 1000) + "K";
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "₺" + val.toLocaleString('tr-TR');
                }
            }
        }
    };
    
    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();
    
    // Category Chart
    var categoryOptions = {
        series: [44, 25, 20, 11],
        chart: {
            type: 'donut',
            height: 350
        },
        labels: ['Elektronik', 'Giyim', 'Ev & Yaşam', 'Diğer'],
        colors: ['#7C3AED', '#2563EB', '#10B981', '#F59E0B'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return Math.round(val) + "%";
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    
    var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
    categoryChart.render();
</script>
@endpush
@endsection