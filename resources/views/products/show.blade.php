@extends('layouts.admin')

@section('title', 'Ürün Detayı')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Ürün Detayı: {{ $product->name }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Düzenle
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Geri
                        </a>
                    </div>
                </div>

                <!-- Approval Actions -->
                @if($product->approval_status == 'pending')
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-yellow-900">Bu ürün onay bekliyor</h3>
                                <p class="mt-1 text-sm text-yellow-700">Ürünü inceleyip onaylayabilir veya reddedebilirsiniz.</p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Onayla
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Product Images -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ürün Görselleri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @if($product->image_models->count() > 0)
                            @foreach($product->image_models as $image)
                                <div class="relative group">
                                    <img src="{{ $image->full_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-lg shadow-sm">
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-4 bg-gray-100 rounded-lg p-8 text-center">
                                <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">Ürün görseli bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ürün Kodu</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->product_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Barkod</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->barcode ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('admin.categories.show', $product->category) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $product->category->name }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marka</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($product->brand)
                                        <a href="{{ route('admin.brands.show', $product->brand) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $product->brand->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                                <dd class="mt-1">
                                    @if($product->status)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Pasif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Onay Durumu</dt>
                                <dd class="mt-1">
                                    @if($product->approval_status == 'approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Onaylı
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Onay Bekliyor
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ürün Durumu</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($product->condition == 'new')
                                        Sıfır
                                    @elseif($product->condition == 'used')
                                        İkinci El
                                    @else
                                        Yenilenmiş
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Pricing and Stock -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Fiyat ve Stok Bilgileri</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Varsayılan Fiyat</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $product->defaultCurrency->formatPrice($product->default_price) }}
                                </dd>
                            </div>
                            @if($product->isOnDiscount())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">İndirimli Fiyat</dt>
                                    <dd class="mt-1 text-lg font-semibold text-green-600">
                                        {{ $product->defaultCurrency->formatPrice($product->discounted_price) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">İndirim Bilgisi</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($product->discount['type'] == 'percentage')
                                            %{{ $product->discount['value'] }} indirim
                                        @else
                                            {{ $product->defaultCurrency->formatPrice($product->discount['value']) }} indirim
                                        @endif
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($product->discount['start_date'])->format('d.m.Y') }} -
                                            {{ \Carbon\Carbon::parse($product->discount['end_date'])->format('d.m.Y') }}
                                        </span>
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stok Miktarı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->stock_quantity }} adet</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Min. Satış Miktarı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->min_sale_quantity }} adet</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Max. Satış Miktarı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->max_sale_quantity ?: 'Sınırsız' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Physical Properties -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Fiziksel Özellikler</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Uzunluk</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->length ? $product->length . ' m' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Genişlik</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->width ? $product->width . ' m' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Yükseklik</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->height ? $product->height . ' m' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ağırlık</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->weight ? $product->weight . ' kg' : '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Description -->
                @if($product->description)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Açıklama</h3>
                        <div class="prose prose-sm max-w-none">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

                <!-- Tags -->
                @if($product->tags && count($product->tags) > 0)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Etiketler</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->tags as $tag)
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Attributes -->
                @if($product->attributes && count($product->attributes) > 0)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Özellikler</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($product->attributes as $attributeId => $value)
                                @php
                                    $attribute = \App\Modules\ProductAttributes\Models\ProductAttribute::find($attributeId);
                                @endphp
                                @if($attribute)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ $attribute->name }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                @endif

                <!-- Vendor Products -->
                @if($product->vendorProducts->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Satıcı Listeleri ({{ $product->vendorProducts->count() }})</h3>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Satıcı
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fiyat
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Durum
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->vendorProducts as $vendorProduct)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $vendorProduct->vendor->full_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $vendorProduct->currency->formatPrice($vendorProduct->price) }}
                                                @if($vendorProduct->isOnDiscount())
                                                    <span class="text-xs text-green-600">
                                                        (İndirimli: {{ $vendorProduct->currency->formatPrice($vendorProduct->discounted_price) }})
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $vendorProduct->stock_quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($vendorProduct->status)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Pasif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- SEO Information -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Bilgileri</h3>
                    <dl class="grid grid-cols-1 gap-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">URL (Slug)</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Başlık</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_title ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Açıklama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_description ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Kelimeler</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_keywords ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Timestamps -->
                <div class="mt-6 text-sm text-gray-500">
                    <p>Oluşturulma: {{ $product->created_at->format('d.m.Y H:i') }}</p>
                    <p>Son Güncelleme: {{ $product->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
