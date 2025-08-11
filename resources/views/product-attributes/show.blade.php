@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Ürün Özelliği: {{ $productAttribute->name }}</h1>
                <div>
                    <a href="{{ route('admin.product-attributes.edit', $productAttribute) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Temel Bilgiler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Temel Bilgiler</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Özellik Adı:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $productAttribute->name }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Özellik Kategorisi:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($productAttribute->attributeCategory)
                                <span class="badge bg-info">
                                    {{ $productAttribute->attributeCategory->name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Durum:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($productAttribute->status)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Pasif</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Sıralama:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-light text-dark">{{ $productAttribute->order }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Özellik Değerleri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Özellik Değerleri 
                        <span class="badge bg-secondary ms-2">{{ count($productAttribute->values) }} değer</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($productAttribute->values as $value)
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="p-2 bg-light rounded text-center">
                                    {{ $value }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- İlişkili Ürün Kategorileri -->
            @if($relatedCategories->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">İlişkili Ürün Kategorileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($relatedCategories as $category)
                                <span class="badge bg-primary">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">İlişkili Ürün Kategorileri</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Bu özellik tüm ürün kategorilerinde kullanılabilir.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Resim -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Özellik Resmi</h5>
                </div>
                <div class="card-body text-center">
                    @if($productAttribute->image)
                        <img src="{{ $productAttribute->image->url }}" 
                             alt="{{ $productAttribute->name }}"
                             class="img-fluid rounded"
                             style="max-height: 250px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <div>
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Resim yüklenmemiş</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- İşlem Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">İşlem Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-calendar-plus text-muted"></i> 
                        <strong>Oluşturulma:</strong>
                        <br>
                        <span class="text-muted">{{ $productAttribute->created_at->format('d.m.Y H:i:s') }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-calendar-check text-muted"></i> 
                        <strong>Son Güncelleme:</strong>
                        <br>
                        <span class="text-muted">{{ $productAttribute->updated_at->format('d.m.Y H:i:s') }}</span>
                    </div>
                    
                    @if($productAttribute->created_at != $productAttribute->updated_at)
                        <div>
                            <i class="fas fa-clock text-muted"></i> 
                            <strong>Geçen Süre:</strong>
                            <br>
                            <span class="text-muted">{{ $productAttribute->updated_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.product-attributes.edit', $productAttribute) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        
                        <form action="{{ route('admin.product-attributes.destroy', $productAttribute) }}" 
                              method="POST" 
                              onsubmit="return confirm('Bu özelliği silmek istediğinizden emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.product-attributes.create') }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus"></i> Yeni Özellik Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: box-shadow 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endpush
