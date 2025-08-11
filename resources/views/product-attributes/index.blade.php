@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Ürün Özellikleri</h1>
                <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Özellik Ekle
                </a>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.product-attributes.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Ara</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Özellik adı...">
                </div>
                <div class="col-md-3">
                    <label for="attribute_category_id" class="form-label">Özellik Kategorisi</label>
                    <select class="form-select" id="attribute_category_id" name="attribute_category_id">
                        <option value="">Tümü</option>
                        @foreach($attributeCategories as $category)
                            <option value="{{ $category->id }}" 
                                {{ request('attribute_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Durum</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tümü</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrele
                    </button>
                    <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Özellikler Tablosu -->
    <div class="card">
        <div class="card-body">
            @if($attributes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">Resim</th>
                                <th>Özellik Adı</th>
                                <th>Kategori</th>
                                <th>Değer Sayısı</th>
                                <th width="100">Sıra</th>
                                <th width="100">Durum</th>
                                <th width="150">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attributes as $attribute)
                                <tr>
                                    <td>
                                        @if($attribute->image)
                                            <img src="{{ $attribute->image->url }}" 
                                                 alt="{{ $attribute->name }}"
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $attribute->name }}</strong>
                                    </td>
                                    <td>
                                        @if($attribute->attributeCategory)
                                            <span class="badge bg-info">
                                                {{ $attribute->attributeCategory->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ count($attribute->values) }} değer
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $attribute->order }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attribute->status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product-attributes.show', $attribute) }}" 
                                               class="btn btn-sm btn-info" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.product-attributes.edit', $attribute) }}" 
                                               class="btn btn-sm btn-warning" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product-attributes.destroy', $attribute) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bu özelliği silmek istediğinizden emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $attributes->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz ürün özelliği eklenmemiş</h5>
                    <p class="text-muted">Yeni özellik ekleyerek başlayın.</p>
                    <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> İlk Özelliği Ekle
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tablo satırlarına hover efekti
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Eğer tıklanan element button veya link değilse
            if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('form')) {
                const showUrl = this.querySelector('a[title="Görüntüle"]')?.href;
                if (showUrl) {
                    window.location.href = showUrl;
                }
            }
        });
    });
</script>
@endpush
