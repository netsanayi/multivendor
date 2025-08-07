@extends('layouts.admin')

@section('title', 'Kategori Detayı')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Kategori Detayı: {{ $category->name }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Düzenle
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Geri
                        </a>
                    </div>
                </div>

                <!-- Category Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Temel Bilgiler</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori Adı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">URL (Slug)</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ url('category/' . $category->slug) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $category->slug }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ana Kategori</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($category->parent)
                                        <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sütun Sayısı</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->column_count }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sıralama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->order }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                                <dd class="mt-1">
                                    @if($category->status)
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
                                <dt class="text-sm font-medium text-gray-500">Oluşturulma Tarihi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('d.m.Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Güncellenme Tarihi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('d.m.Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Image and Description -->
                    <div class="space-y-6">
                        <!-- Category Image -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Kategori Resmi</h3>
                            @if($category->image)
                                <img src="{{ $category->image->full_url }}" alt="{{ $category->name }}" class="w-full max-w-xs rounded-lg shadow-sm">
                            @else
                                <p class="text-sm text-gray-500">Resim yüklenmemiş</p>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($category->description)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Açıklama</h3>
                                <div class="prose prose-sm max-w-none">
                                    {!! $category->description !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SEO Information -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Bilgileri</h3>
                    <dl class="grid grid-cols-1 gap-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Başlık</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->meta_title ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Açıklama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->meta_description ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Kelimeler</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->meta_keywords ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Child Categories -->
                @if($category->children->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Alt Kategoriler ({{ $category->children->count() }})</h3>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <ul class="divide-y divide-gray-200">
                                @foreach($category->children as $child)
                                    <li class="px-4 py-3 hover:bg-gray-100">
                                        <a href="{{ route('admin.categories.show', $child) }}" class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">{{ $child->name }}</span>
                                            <span class="text-sm text-gray-500">{{ $child->products->count() }} ürün</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Products -->
                @if($category->products->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ürünler ({{ $category->products->count() }})</h3>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <ul class="divide-y divide-gray-200">
                                @foreach($category->products->take(10) as $product)
                                    <li class="px-4 py-3 hover:bg-gray-100">
                                        <a href="{{ route('admin.products.show', $product) }}" class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ $product->product_code }}</span>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ number_format($product->default_price, 2) }} ₺</span>
                                        </a>
                                    </li>
                                @endforeach
                                @if($category->products->count() > 10)
                                    <li class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            Tüm ürünleri görüntüle ({{ $category->products->count() }})
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
