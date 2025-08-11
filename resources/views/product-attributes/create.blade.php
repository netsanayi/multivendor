@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Yeni Ürün Özelliği Ekle</h1>
                <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.product-attributes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Temel Bilgiler -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Temel Bilgiler</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Özellik Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Örn: Renk, Beden, Hafıza Kapasitesi</small>
                        </div>

                        <div class="mb-3">
                            <label for="attribute_category_id" class="form-label">Özellik Kategorisi <span class="text-danger">*</span></label>
                            <select class="form-select @error('attribute_category_id') is-invalid @enderror" 
                                    id="attribute_category_id" name="attribute_category_id" required>
                                <option value="">Kategori Seçin</option>
                                @foreach($attributeCategories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('attribute_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('attribute_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="product_category_ids" class="form-label">Ürün Kategorileri</label>
                            <select class="form-select @error('product_category_ids') is-invalid @enderror" 
                                    id="product_category_ids" name="product_category_ids[]" multiple>
                                @foreach($productCategories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ in_array($category->id, old('product_category_ids', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Seçim yapmazsanız tüm kategorilerde kullanılabilir. Birden fazla kategori seçmek için Ctrl tuşunu basılı tutun.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Özellik Değerleri -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Özellik Değerleri <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div id="values-container">
                            <div class="value-item mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control @error('values.0') is-invalid @enderror" 
                                           name="values[]" placeholder="Değer girin" value="{{ old('values.0') }}" required>
                                    <button type="button" class="btn btn-danger remove-value" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @error('values.0')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="add-value">
                            <i class="fas fa-plus"></i> Değer Ekle
                        </button>
                        <small class="form-text text-muted d-block mt-2">
                            Örn: Renk için: Kırmızı, Mavi, Yeşil | Beden için: S, M, L, XL
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Resim -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Özellik Resmi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label">Resim Seç</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Maksimum 2MB, JPEG, PNG, JPG veya GIF formatında
                            </small>
                        </div>
                        <div id="image-preview" class="text-center" style="display: none;">
                            <img src="" alt="Önizleme" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- Ayarlar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ayarlar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="order" class="form-label">Sıralama <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', 0) }}" min="0" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Küçük sayılar önce gösterilir</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Durum <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Pasif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                    <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> İptal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Değer ekleme/silme işlemleri
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('values-container');
        const addButton = document.getElementById('add-value');
        
        // Yeni değer ekle
        addButton.addEventListener('click', function() {
            const valueItem = document.createElement('div');
            valueItem.className = 'value-item mb-2';
            valueItem.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="values[]" placeholder="Değer girin" required>
                    <button type="button" class="btn btn-danger remove-value">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(valueItem);
            updateRemoveButtons();
        });
        
        // Değer sil
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-value')) {
                e.target.closest('.value-item').remove();
                updateRemoveButtons();
            }
        });
        
        // Silme butonlarını güncelle
        function updateRemoveButtons() {
            const items = container.querySelectorAll('.value-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-value');
                if (items.length > 1) {
                    removeBtn.style.display = 'block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }
        
        // Resim önizleme
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = imagePreview.querySelector('img');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
        
        // Başlangıçta silme butonlarını güncelle
        updateRemoveButtons();
    });
</script>
@endpush

@push('styles')
<style>
    .value-item {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
