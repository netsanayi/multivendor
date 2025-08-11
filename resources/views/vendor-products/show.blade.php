@extends('layouts.admin')

@section('title', 'Satıcı Ürünü Detayları')
@section('page-title', 'Satıcı Ürünü Detayları')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.vendor-products.index') }}">Satıcı Ürünleri</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detay</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Ürün Bilgileri -->
            <div class="admin-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Ürün Bilgileri</h5>
                    <div>
                        @if($vendorProduct->is_active ?? $vendorProduct->status)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Pasif</span>
                        @endif
                        @if($vendorProduct->is_featured)
                            <span class="badge bg-warning">Öne Çıkan</span>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ürün</label>
                        <div class="fw-bold">
                            @if($vendorProduct->product)
                                {{ $vendorProduct->product->name }}
                                <small class="text-muted d-block">SKU: {{ $vendorProduct->product->sku ?? 'N/A' }}</small>
                            @else
                                <span class="text-muted">Ürün bulunamadı</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Satıcı</label>
                        <div class="fw-bold">
                            @if($vendorProduct->vendor)
                                {{ $vendorProduct->vendor->name }}
                                <small class="text-muted d-block">{{ $vendorProduct->vendor->email }}</small>
                            @else
                                <span class="text-muted">Satıcı bulunamadı</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Satıcı SKU</label>
                        <div class="fw-bold">{{ $vendorProduct->sku ?? 'Belirtilmemiş' }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Barkod</label>
                        <div class="fw-bold">{{ $vendorProduct->barcode ?? 'Belirtilmemiş' }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ürün Durumu</label>
                        <div>
                            @switch($vendorProduct->condition)
                                @case('new')
                                    <span class="badge bg-primary">Yeni</span>
                                    @break
                                @case('used')
                                    <span class="badge bg-secondary">Kullanılmış</span>
                                    @break
                                @case('refurbished')
                                    <span class="badge bg-info">Yenilenmiş</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">Belirtilmemiş</span>
                            @endswitch
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Stok Durumu</label>
                        <div>
                            @switch($vendorProduct->availability ?? 'in_stock')
                                @case('in_stock')
                                    <span class="badge bg-success">Stokta</span>
                                    @break
                                @case('out_of_stock')
                                    <span class="badge bg-danger">Stokta Yok</span>
                                    @break
                                @case('pre_order')
                                    <span class="badge bg-info">Ön Sipariş</span>
                                    @break
                                @case('discontinued')
                                    <span class="badge bg-secondary">Üretimi Durduruldu</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fiyat ve Stok Bilgileri -->
            <div class="admin-card mb-4">
                <h5 class="mb-4">Fiyat ve Stok Bilgileri</h5>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Satış Fiyatı</label>
                        <div class="fw-bold fs-4 text-primary">{{ number_format($vendorProduct->price ?? 0, 2) }} TL</div>
                    </div>
                    
                    @if($vendorProduct->compare_price)
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Karşılaştırma Fiyatı</label>
                        <div class="fw-bold text-decoration-line-through text-muted">
                            {{ number_format($vendorProduct->compare_price, 2) }} TL
                        </div>
                    </div>
                    @endif
                    
                    @if($vendorProduct->cost)
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Maliyet</label>
                        <div class="fw-bold">{{ number_format($vendorProduct->cost, 2) }} TL</div>
                    </div>
                    @endif
                    
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Stok Miktarı</label>
                        <div class="fw-bold">
                            @if(($vendorProduct->quantity ?? $vendorProduct->stock_quantity ?? 0) > 10)
                                <span class="text-success">{{ $vendorProduct->quantity ?? $vendorProduct->stock_quantity ?? 0 }} adet</span>
                            @elseif(($vendorProduct->quantity ?? $vendorProduct->stock_quantity ?? 0) > 0)
                                <span class="text-warning">{{ $vendorProduct->quantity ?? $vendorProduct->stock_quantity ?? 0 }} adet</span>
                            @else
                                <span class="text-danger">Stokta yok</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Min. Satış Miktarı</label>
                        <div class="fw-bold">{{ $vendorProduct->min_quantity ?? $vendorProduct->min_sale_quantity ?? 1 }} adet</div>
                    </div>
                    
                    @if($vendorProduct->max_quantity ?? $vendorProduct->max_sale_quantity)
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Maks. Satış Miktarı</label>
                        <div class="fw-bold">{{ $vendorProduct->max_quantity ?? $vendorProduct->max_sale_quantity }} adet</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Komisyon Bilgileri -->
            @if($vendorProduct->commission_rate)
            <div class="admin-card mb-4">
                <h5 class="mb-4">Komisyon Bilgileri</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Komisyon Tipi</label>
                        <div class="fw-bold">
                            {{ $vendorProduct->commission_type == 'percentage' ? 'Yüzde' : 'Sabit' }}
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Komisyon Oranı/Tutarı</label>
                        <div class="fw-bold">
                            {{ $vendorProduct->commission_rate }}{{ $vendorProduct->commission_type == 'percentage' ? '%' : ' TL' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Özellikler -->
            <div class="admin-card mb-4">
                <h5 class="mb-4">Özellikler</h5>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->is_active ?? $vendorProduct->status ? 'checked' : '' }}>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->is_featured ? 'checked' : '' }}>
                        <label class="form-check-label">Öne Çıkan</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->track_inventory ?? true ? 'checked' : '' }}>
                        <label class="form-check-label">Stok Takibi</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->allow_backorders ? 'checked' : '' }}>
                        <label class="form-check-label">Stok Bittiğinde Satışa İzin Ver</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->is_digital ? 'checked' : '' }}>
                        <label class="form-check-label">Dijital Ürün</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->is_virtual ? 'checked' : '' }}>
                        <label class="form-check-label">Sanal Ürün</label>
                    </div>
                </div>
                
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled 
                               {{ $vendorProduct->requires_shipping ?? true ? 'checked' : '' }}>
                        <label class="form-check-label">Kargo Gerekli</label>
                    </div>
                </div>
            </div>
            
            <!-- Tarih Bilgileri -->
            <div class="admin-card mb-4">
                <h5 class="mb-4">Tarih Bilgileri</h5>
                
                <div class="mb-3">
                    <label class="text-muted small">Oluşturulma Tarihi</label>
                    <div>{{ $vendorProduct->created_at->format('d.m.Y H:i') }}</div>
                </div>
                
                <div>
                    <label class="text-muted small">Son Güncelleme</label>
                    <div>{{ $vendorProduct->updated_at->format('d.m.Y H:i') }}</div>
                </div>
            </div>
            
            <!-- İşlemler -->
            <div class="admin-card">
                <h5 class="mb-4">İşlemler</h5>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.vendor-products.edit', $vendorProduct) }}" class="btn btn-warning">
                        <i class="ki-duotone ki-pencil fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Düzenle
                    </a>
                    
                    <form action="{{ route('admin.vendor-products.destroy', $vendorProduct) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Bu satıcı ürününü silmek istediğinizden emin misiniz?')">
                            <i class="ki-duotone ki-trash fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Sil
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.vendor-products.index') }}" class="btn btn-light">
                        <i class="ki-duotone ki-arrow-left fs-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
