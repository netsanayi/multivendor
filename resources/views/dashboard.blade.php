@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Hoş Geldiniz, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-purple-100">{{ now()->locale('tr')->isoFormat('DD MMMM YYYY, dddd') }} - Harika bir gün olsun!</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                    <i class="ki-duotone ki-plus-square fs-5 mr-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Yeni Ürün Ekle
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sales -->
        <div class="custom-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="ki-duotone ki-dollar text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                <span class="text-green-500 text-sm font-semibold">+12.5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">₺45,678</h3>
            <p class="text-gray-500 text-sm mt-1">Toplam Satış</p>
        </div>
        
        <!-- Orders -->
        <div class="custom-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="ki-duotone ki-basket text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </div>
                <span class="text-blue-500 text-sm font-semibold">+8.2%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">234</h3>
            <p class="text-gray-500 text-sm mt-1">Toplam Sipariş</p>
        </div>
        
        <!-- Products -->
        <div class="custom-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="ki-duotone ki-package text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                <span class="text-purple-500 text-sm font-semibold">+4</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">89</h3>
            <p class="text-gray-500 text-sm mt-1">Aktif Ürün</p>
        </div>
        
        <!-- Customers -->
        <div class="custom-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="ki-duotone ki-people text-white fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </div>
                <span class="text-orange-500 text-sm font-semibold">+23</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">1,234</h3>
            <p class="text-gray-500 text-sm mt-1">Müşteriler</p>
        </div>
    </div>
    
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - 2/3 width -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sales Chart -->
            <div class="custom-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Satış Grafiği</h2>
                    <select class="bg-gray-100 border-0 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option>Bu Ay</option>
                        <option>Son 3 Ay</option>
                        <option>Bu Yıl</option>
                    </select>
                </div>
                <div id="salesChart" style="height: 300px;"></div>
            </div>
            
            <!-- Recent Orders -->
            <div class="custom-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Son Siparişler</h2>
                    <a href="#" class="text-purple-600 hover:text-purple-700 text-sm font-semibold">Tümünü Gör →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Sipariş No</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Müşteri</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Tutar</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Durum</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 text-sm">#ORD-2024-001</td>
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-2.png') }}" alt="" class="w-8 h-8 rounded-full">
                                        <span class="text-sm">Ahmet Yılmaz</span>
                                    </div>
                                </td>
                                <td class="py-4 text-sm font-semibold">₺1,234</td>
                                <td class="py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded-full">Tamamlandı</span>
                                </td>
                                <td class="py-4 text-sm text-gray-500">5 dk önce</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 text-sm">#ORD-2024-002</td>
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-3.png') }}" alt="" class="w-8 h-8 rounded-full">
                                        <span class="text-sm">Ayşe Demir</span>
                                    </div>
                                </td>
                                <td class="py-4 text-sm font-semibold">₺856</td>
                                <td class="py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-yellow-600 bg-yellow-100 rounded-full">Hazırlanıyor</span>
                                </td>
                                <td class="py-4 text-sm text-gray-500">15 dk önce</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 text-sm">#ORD-2024-003</td>
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ asset('assets/html/dist/assets/media/avatars/300-4.png') }}" alt="" class="w-8 h-8 rounded-full">
                                        <span class="text-sm">Mehmet Kaya</span>
                                    </div>
                                </td>
                                <td class="py-4 text-sm font-semibold">₺2,345</td>
                                <td class="py-4">
                                    <span class="px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded-full">Kargoda</span>
                                </td>
                                <td class="py-4 text-sm text-gray-500">1 saat önce</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(auth()->user()->hasRole('vendor'))
                <a href="{{ route('vendor.dashboard') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-shop text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Satıcı Paneli</h3>
                    <p class="text-sm text-gray-500 mt-2">Ürün ve sipariş yönetimi</p>
                </a>
                @endif
                
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-shield-tick text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Yönetim Paneli</h3>
                    <p class="text-sm text-gray-500 mt-2">Sistem yönetimi</p>
                </a>
                @endif
                
                <a href="{{ route('wishlist.index') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-400 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-heart text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">İstek Listem</h3>
                    <p class="text-sm text-gray-500 mt-2">Favori ürünleriniz</p>
                </a>
                
                <a href="{{ route('messages.index') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-messages text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Mesajlar</h3>
                    <p class="text-sm text-gray-500 mt-2">İletişim merkezi</p>
                </a>
                
                <a href="{{ route('profile.show') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-user text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Profilim</h3>
                    <p class="text-sm text-gray-500 mt-2">Hesap ayarları</p>
                </a>
                
                <a href="{{ route('tickets.index') }}" class="custom-card p-6 text-center hover:border-purple-500 border-2 border-transparent transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-duotone ki-support-24 text-white fs-2x">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Destek</h3>
                    <p class="text-sm text-gray-500 mt-2">Yardım merkezi</p>
                </a>
            </div>
        </div>
        
        <!-- Right Column - 1/3 width -->
        <div class="space-y-6">
            <!-- Top Products -->
            <div class="custom-card p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">En Çok Satan Ürünler</h2>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('assets/html/dist/assets/media/store/client/600x600/1.png') }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Kablosuz Kulaklık</h4>
                            <p class="text-xs text-gray-500">45 satış</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">₺899</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('assets/html/dist/assets/media/store/client/600x600/2.png') }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Akıllı Saat</h4>
                            <p class="text-xs text-gray-500">38 satış</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">₺1,299</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('assets/html/dist/assets/media/store/client/600x600/3.png') }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Laptop Çantası</h4>
                            <p class="text-xs text-gray-500">32 satış</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">₺349</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('assets/html/dist/assets/media/store/client/600x600/4.png') }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Powerbank</h4>
                            <p class="text-xs text-gray-500">28 satış</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">₺249</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="custom-card p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Son Aktiviteler</h2>
                <div class="space-y-4">
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Yeni sipariş alındı <span class="font-semibold">#ORD-2024-004</span></p>
                            <p class="text-xs text-gray-500">2 dakika önce</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Ürün güncellendi: <span class="font-semibold">Kablosuz Mouse</span></p>
                            <p class="text-xs text-gray-500">15 dakika önce</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Stok uyarısı: <span class="font-semibold">USB Kablo</span> (5 adet kaldı)</p>
                            <p class="text-xs text-gray-500">30 dakika önce</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Yeni yorum: 5 yıldız - <span class="font-semibold">Ahmet Y.</span></p>
                            <p class="text-xs text-gray-500">1 saat önce</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Support Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-6 text-white">
                <i class="ki-duotone ki-question fs-3x mb-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <h3 class="text-xl font-bold mb-2">Yardıma mı ihtiyacınız var?</h3>
                <p class="text-purple-100 text-sm mb-4">Destek ekibimiz size yardımcı olmak için hazır.</p>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                    <i class="ki-duotone ki-message-question fs-5 mr-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Destek Al
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sales Chart
    var options = {
        series: [{
            name: 'Satışlar',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 69, 72, 75]
        }],
        chart: {
            type: 'area',
            height: 300,
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
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "₺" + val + " bin"
                }
            }
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#salesChart"), options);
    chart.render();
</script>
@endpush
@endsection