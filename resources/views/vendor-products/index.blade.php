@extends('layouts.admin')

@section('title', 'Satıcı Ürünleri')
@section('page-title', 'Satıcı Ürünleri')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Satıcı Ürünleri</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filtreler -->
    <div class="admin-card mb-4">
        <form method="GET" action="{{ route('admin.vendor-products.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Ara</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Ürün adı, SKU...">
            </div>
            <div class="col-md-3">
                <label for="vendor_id" class="form-label">Satıcı</label>
                <select class="form-select" id="vendor_id" name="vendor_id">
                    <option value="">Tüm Satıcılar</option>
                    @foreach($vendors ?? [] as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tümü</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="condition" class="form-label">Ürün Durumu</label>
                <select class="form-select" id="condition" name="condition">
                    <option value="">Tümü</option>
                    <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>Yeni</option>
                    <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Kullanılmış</option>
                    <option value="refurbished" {{ request('condition') === 'refurbished' ? 'selected' : '' }}>Yenilenmiş</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="ki-duotone ki-magnifier fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Filtrele
                </button>
                <a href="{{ route('admin.vendor-products.index') }}" class="btn btn-secondary">
                    <i class="ki-duotone ki-cross-circle fs-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Tablo -->
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Satıcı Ürün Listesi</h5>
            <a href="{{ route('admin.vendor-products.create') }}" class="btn btn-success">
                <i class="ki-duotone ki-plus fs-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Yeni Satıcı Ürünü
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Ürün</th>
                        <th>Satıcı</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Durum</th>
                        <th>Ürün Durumu</th>
                        <th width="150">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendorProducts as $vendorProduct)
                    <tr>
                        <td>{{ $vendorProduct->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($vendorProduct->product)
                                    <div>
                                        <div class="fw-bold">{{ $vendorProduct->product->name }}</div>
                                        <small class="text-muted">SKU: {{ $vendorProduct->sku ?? 'N/A' }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Ürün bulunamadı</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($vendorProduct->vendor)
                                <div>
                                    <div>{{ $vendorProduct->vendor->name }}</div>
                                    <small class="text-muted">{{ $vendorProduct->vendor->email }}</small>
                                </div>
                            @else
                                <span class="text-muted">Satıcı bulunamadı</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ number_format($vendorProduct->price ?? 0, 2) }} TL</div>
                            @if($vendorProduct->compare_price)
                                <small class="text-muted text-decoration-line-through">
                                    {{ number_format($vendorProduct->compare_price, 2) }} TL
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($vendorProduct->quantity > 10)
                                <span class="badge bg-success">{{ $vendorProduct->quantity ?? 0 }} adet</span>
                            @elseif($vendorProduct->quantity > 0)
                                <span class="badge bg-warning">{{ $vendorProduct->quantity ?? 0 }} adet</span>
                            @else
                                <span class="badge bg-danger">Stokta yok</span>
                            @endif
                        </td>
                        <td>
                            @if($vendorProduct->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Pasif</span>
                            @endif
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.vendor-products.show', $vendorProduct) }}" 
                                   class="btn btn-sm btn-light-primary" title="Görüntüle">
                                    <i class="ki-duotone ki-eye fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </a>
                                <a href="{{ route('admin.vendor-products.edit', $vendorProduct) }}" 
                                   class="btn btn-sm btn-light-warning" title="Düzenle">
                                    <i class="ki-duotone ki-pencil fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                                <form action="{{ route('admin.vendor-products.destroy', $vendorProduct) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light-danger" 
                                            title="Sil"
                                            onclick="return confirm('Bu satıcı ürününü silmek istediğinizden emin misiniz?')">
                                        <i class="ki-duotone ki-trash fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="ki-duotone ki-package fs-4x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <p class="text-muted">Henüz satıcı ürünü bulunmamaktadır.</p>
                            <a href="{{ route('admin.vendor-products.create') }}" class="btn btn-primary">
                                İlk Satıcı Ürününü Ekle
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendorProducts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $vendorProducts->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Select2 for dropdowns
    $(document).ready(function() {
        $('#vendor_id').select2({
            placeholder: 'Satıcı seçin',
            allowClear: true
        });
    });
</script>
@endpush
