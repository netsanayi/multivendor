@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Toplam Ürün -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Toplam Ürün
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ \App\Modules\Products\Models\Product::count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.products.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Tümünü görüntüle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Toplam Satıcı -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Toplam Satıcı
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ \App\Modules\Users\Models\User::role('vendor')->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Tümünü görüntüle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Onay Bekleyen Ürünler -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Onay Bekleyen
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ \App\Modules\Products\Models\Product::pending()->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.products.index', ['approval_status' => 'pending']) }}" class="font-medium text-yellow-600 hover:text-yellow-500">
                            Görüntüle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Aktif Banner -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Aktif Banner
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ \App\Modules\Banners\Models\Banner::active()->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.banners.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Yönet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Aktiviteler ve Grafikler -->
        <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Son Eklenen Ürünler -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Son Eklenen Ürünler
                    </h3>
                    <div class="mt-5">
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach(\App\Modules\Products\Models\Product::with(['category', 'brand'])->latest()->take(5)->get() as $product)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $product->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $product->category->name }} • {{ $product->brand ? $product->brand->name : 'Markasız' }}
                                            </p>
                                        </div>
                                        <div>
                                            @if($product->approval_status == 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Onaylı
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Bekliyor
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('admin.products.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son Kayıt Olan Satıcılar -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Son Kayıt Olan Satıcılar
                    </h3>
                    <div class="mt-5">
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach(\App\Modules\Users\Models\User::role('vendor')->latest()->take(5)->get() as $vendor)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500">
                                                <span class="text-sm font-medium leading-none text-white">{{ $vendor->initials }}</span>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $vendor->full_name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $vendor->email }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">
                                                {{ $vendor->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('admin.users.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori İstatistikleri -->
        <div class="mt-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Kategori Bazlı Ürün Dağılımı
                    </h3>
                    <div class="mt-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach(\App\Modules\Categories\Models\Category::whereNull('parent_id')->withCount('products')->orderBy('products_count', 'desc')->take(4)->get() as $category)
                            <div class="bg-gray-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ $category->name }}
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $category->products_count }}
                                </dd>
                                <dd class="mt-1">
                                    <div class="flex items-center text-sm">
                                        <span class="text-green-600 font-medium">{{ $category->children->count() }}</span>
                                        <span class="ml-2 text-gray-500">alt kategori</span>
                                    </div>
                                </dd>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
