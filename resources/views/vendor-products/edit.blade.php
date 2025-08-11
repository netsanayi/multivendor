@extends('layouts.admin')

@section('title', 'Satıcı Ürünü Düzenle')
@section('page-title', 'Satıcı Ürünü Düzenle')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.vendor-products.index') }}">Satıcı Ürünleri</a></li>
        <li class="breadcrumb-item active" aria-current="page">Düzenle</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.vendor-products.update', $vendorProduct) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Temel Bilgiler -->
                <div class="admin-card mb-4">
                    <h5 class="mb-4">Temel Bilgiler</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Ürün <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" 
                                    id="product_id" name="product_id" required>
                                <option value="">Ürün seçin</option>
                                @foreach($products ?? [] as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $vendorProduct->product_id ?? $vendorProduct->relation_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku ?? 'SKU yok' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="vendor_id" class="form-label">Satıcı <span class="text-danger">*</span></label>
                            <select class="form-select @error('vendor_id') is-invalid @enderror" 
                                    id="vendor_id" name="vendor_id" required>
                                <option value="">Satıcı seçin</option>
                                @foreach($vendors ?? [] as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $vendorProduct->vendor_id ?? $vendorProduct->user_relation_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }} ({{ $vendor->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" name="sku" value="{{ old('sku', $vendorProduct->sku) }}">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Barkod</label>
                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                   id="barcode" name="barcode" value="{{ old('barcode', $vendorProduct->barcode) }}">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Fiyat ve Stok Bilgileri -->
                <div class="admin-card mb-4">
                    <h5 class="mb-4">Fiyat ve Stok Bilgileri</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Satış Fiyatı <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $vendorProduct->price) }}" step="0.01" required>
                                <span class="input-group-text">TL</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="compare_price" class="form-label">Karşılaştırma Fiyatı</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('compare_price') is-invalid @enderror" 
                                       id="compare_price" name="compare_price" value="{{ old('compare_price', $vendorProduct->compare_price) }}" step="0.01">
                                <span class="input-group-text">TL</span>
                            </div>
                            @error('compare_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="cost" class="form-label">Maliyet</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                       id="cost" name="cost" value="{{ old('cost', $vendorProduct->cost) }}" step="0.01">
                                <span class="input-group-text">TL</span>
                            </div>
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Stok Miktarı <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity', $vendorProduct->quantity ?? $vendorProduct->stock_quantity) }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="min_quantity" class="form-label">Minimum Satış Miktarı</label>
                            <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" 
                                   id="min_quantity" name="min_quantity" value="{{ old('min_quantity', $vendorProduct->min_quantity ?? $vendorProduct->min_sale_quantity ?? 1) }}" min="1">
                            @error('min_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="max_quantity" class="form-label">Maksimum Satış Miktarı</label>
                            <input type="number" class="form-control @error('max_quantity') is-invalid @enderror" 
                                   id="max_quantity" name="max_quantity" value="{{ old('max_quantity', $vendorProduct->max_quantity ?? $vendorProduct->max_sale_quantity) }}" min="1">
                            @error('max_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Komisyon Bilgileri -->
                <div class="admin-card mb-4">
                    <h5 class="mb-4">Komisyon Bilgileri</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="commission_type" class="form-label">Komisyon Tipi</label>
                            <select class="form-select @error('commission_type') is-invalid @enderror" 
                                    id="commission_type" name="commission_type">
                                <option value="percentage" {{ old('commission_type', $vendorProduct->commission_type) == 'percentage' ? 'selected' : '' }}>Yüzde</option>
                                <option value="fixed" {{ old('commission_type', $vendorProduct->commission_type) == 'fixed' ? 'selected' : '' }}>Sabit</option>
                            </select>
                            @error('commission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="commission_rate" class="form-label">Komisyon Oranı/Tutarı</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" 
                                       id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $vendorProduct->commission_rate) }}" 
                                       step="0.01" min="0" max="100">
                                <span class="input-group-text" id="commission_suffix">{{ $vendorProduct->commission_type == 'fixed' ? 'TL' : '%' }}</span>
                            </div>
                            @error('commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Durum Bilgileri -->
                <div class="admin-card mb-4">
                    <h5 class="mb-4">Durum Bilgileri</h5>
                    
                    <div class="mb-3">
                        <label for="condition" class="form-label">Ürün Durumu</label>
                        <select class="form-select @error('condition') is-invalid @enderror" 
                                id="condition" name="condition">
                            <option value="new" {{ old('condition', $vendorProduct->condition) == 'new' ? 'selected' : '' }}>Yeni</option>
                            <option value="used" {{ old('condition', $vendorProduct->condition) == 'used' ? 'selected' : '' }}>Kullanılmış</option>
                            <option value="refurbished" {{ old('condition', $vendorProduct->condition) == 'refurbished' ? 'selected' : '' }}>Yenilenmiş</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="availability" class="form-label">Stok Durumu</label>
                        <select class="form-select @error('availability') is-invalid @enderror" 
                                id="availability" name="availability">
                            <option value="in_stock" {{ old('availability', $vendorProduct->availability) == 'in_stock' ? 'selected' : '' }}>Stokta</option>
                            <option value="out_of_stock" {{ old('availability', $vendorProduct->availability) == 'out_of_stock' ? 'selected' : '' }}>Stokta Yok</option>
                            <option value="pre_order" {{ old('availability', $vendorProduct->availability) == 'pre_order' ? 'selected' : '' }}>Ön Sipariş</option>
                            <option value="discontinued" {{ old('availability', $vendorProduct->availability) == 'discontinued' ? 'selected' : '' }}>Üretimi Durduruldu</option>
                        </select>
                        @error('availability')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               value="1" {{ old('is_active', $vendorProduct->is_active ?? $vendorProduct->status) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                               value="1" {{ old('is_featured', $vendorProduct->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Öne Çıkan</label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="track_inventory" name="track_inventory" 
                               value="1" {{ old('track_inventory', $vendorProduct->track_inventory ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="track_inventory">Stok Takibi</label>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_backorders" name="allow_backorders" 
                               value="1" {{ old('allow_backorders', $vendorProduct->allow_backorders) ? 'checked' : '' }}>
                        <label class="form-check-label" for="allow_backorders">Stok Bittiğinde Satışa İzin Ver</label>
                    </div>
                </div>
                
                <!-- İşlemler -->
                <div class="admin-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Güncelle
                        </button>
                        <a href="{{ route('admin.vendor-products.index') }}" class="btn btn-light">
                            <i class="ki-duotone ki-arrow-left fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Commission type change
    document.getElementById('commission_type').addEventListener('change', function() {
        const suffix = document.getElementById('commission_suffix');
        if (this.value === 'percentage') {
            suffix.textContent = '%';
            document.getElementById('commission_rate').max = 100;
        } else {
            suffix.textContent = 'TL';
            document.getElementById('commission_rate').removeAttribute('max');
        }
    });
    
    // Select2
    $(document).ready(function() {
        $('#product_id, #vendor_id').select2({
            placeholder: 'Seçin...',
            allowClear: true
        });
    });
</script>
@endpush
