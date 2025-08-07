@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Marka Detayı: {{ $brand->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($brand->image)
                                <img src="{{ $brand->image->url }}" alt="{{ $brand->name }}" 
                                     class="img-fluid img-thumbnail">
                            @else
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Resim yüklenmemiş</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">ID</th>
                                    <td>{{ $brand->id }}</td>
                                </tr>
                                <tr>
                                    <th>Marka Adı</th>
                                    <td>{{ $brand->name }}</td>
                                </tr>
                                <tr>
                                    <th>Sıralama</th>
                                    <td>{{ $brand->order }}</td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        @if($brand->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kategoriler</th>
                                    <td>
                                        @if($brand->product_category_ids && count($brand->product_category_ids) > 0)
                                            @php
                                                $categoryNames = \App\Modules\Categories\Models\Category::whereIn('id', $brand->product_category_ids)->pluck('name');
                                            @endphp
                                            @foreach($categoryNames as $categoryName)
                                                <span class="badge badge-info">{{ $categoryName }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tüm kategoriler</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma Tarihi</th>
                                    <td>{{ $brand->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $brand->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($brand->products && $brand->products->count() > 0)
                    <div class="mt-4">
                        <h4>Bu Markaya Ait Ürünler ({{ $brand->products->count() }})</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ürün Adı</th>
                                        <th>Kategori</th>
                                        <th>Fiyat</th>
                                        <th>Stok</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brand->products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ number_format($product->default_price, 2) }} {{ $product->defaultCurrency->symbol ?? 'TL' }}</td>
                                        <td>{{ $product->stock_quantity }}</td>
                                        <td>
                                            @if($product->status)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Görüntüle
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
