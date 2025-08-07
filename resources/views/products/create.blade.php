@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Ürün Ekle</h3>
                </div>
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">Genel Bilgiler</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pricing-tab" data-toggle="tab" href="#pricing" role="tab">Fiyat & Stok</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab">SEO</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="attributes-tab" data-toggle="tab" href="#attributes" role="tab">Özellikler</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab">Resimler</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content mt-3">
                            <!-- Genel Bilgiler -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Ürün Adı <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_code">Ürün Kodu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('product_code') is-invalid @enderror" 
                                                   id="product_code" name="product_code" value="{{ old('product_code') }}" required>
                                            @error('product_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id" required>
                                                <option value="">Kategori Seçin</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_id">Marka</label>
                                            <select class="form-control select2 @error('brand_id') is-invalid @enderror" 
                                                    id="brand_id" name="brand_id">
                                                <option value="">Marka Seçin</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Açıklama</label>
                                    <textarea class="form-control summernote @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="5">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="barcode">Barkod</label>
                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                                   id="barcode" name="barcode" value="{{ old('barcode') }}">
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="condition">Ürün Durumu <span class="text-danger">*</span></label>
                                            <select class="form-control @error('condition') is-invalid @enderror" 
                                                    id="condition" name="condition" required>
                                                <option value="new" {{ old('condition', 'new') == 'new' ? 'selected' : '' }}>Sıfır</option>
                                                <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>İkinci El</option>
                                                <option value="refurbished" {{ old('condition') == 'refurbished' ? 'selected' : '' }}>Yenilenmiş</option>
                                            </select>
                                            @error('condition')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fiyat & Stok -->
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_price">Varsayılan Fiyat <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('default_price') is-invalid @enderror" 
                                                   id="default_price" name="default_price" value="{{ old('default_price') }}" 
                                                   step="0.01" min="0" required>
                                            @error('default_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_currency_id">Para Birimi <span class="text-danger">*</span></label>
                                            <select class="form-control @error('default_currency_id') is-invalid @enderror" 
                                                    id="default_currency_id" name="default_currency_id" required>
                                                @foreach($currencies as $currency)
                                                    <option value="{{ $currency->id }}" {{ old('default_currency_id', 1) == $currency->id ? 'selected' : '' }}>
                                                        {{ $currency->name }} ({{ $currency->symbol }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('default_currency_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="stock_quantity">Stok Adedi <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" 
                                                   min="0" required>
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="min_sale_quantity">Min. Satış Miktarı <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('min_sale_quantity') is-invalid @enderror" 
                                                   id="min_sale_quantity" name="min_sale_quantity" value="{{ old('min_sale_quantity', 1) }}" 
                                                   min="1" required>
                                            @error('min_sale_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_sale_quantity">Maks. Satış Miktarı <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('max_sale_quantity') is-invalid @enderror" 
                                                   id="max_sale_quantity" name="max_sale_quantity" value="{{ old('max_sale_quantity', 10) }}" 
                                                   min="1" required>
                                            @error('max_sale_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h5>Ürün Boyutları</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="length">Uzunluk (m)</label>
                                            <input type="number" class="form-control @error('length') is-invalid @enderror" 
                                                   id="length" name="length" value="{{ old('length') }}" 
                                                   step="0.01" min="0">
                                            @error('length')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="width">Genişlik (m)</label>
                                            <input type="number" class="form-control @error('width') is-invalid @enderror" 
                                                   id="width" name="width" value="{{ old('width') }}" 
                                                   step="0.01" min="0">
                                            @error('width')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="height">Yükseklik (m)</label>
                                            <input type="number" class="form-control @error('height') is-invalid @enderror" 
                                                   id="height" name="height" value="{{ old('height') }}" 
                                                   step="0.01" min="0">
                                            @error('height')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="weight">Ağırlık (kg)</label>
                                            <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                                   id="weight" name="weight" value="{{ old('weight') }}" 
                                                   step="0.01" min="0">
                                            @error('weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="form-group">
                                    <label for="meta_title">Meta Başlık</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Arama motorlarında görünecek başlık</small>
                                </div>

                                <div class="form-group">
                                    <label for="meta_description">Meta Açıklama</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Arama motorlarında görünecek açıklama</small>
                                </div>

                                <div class="form-group">
                                    <label for="meta_keywords">Meta Anahtar Kelimeler</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                           id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Virgülle ayrılmış anahtar kelimeler</small>
                                </div>

                                <div class="form-group">
                                    <label for="tags">Etiketler</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                           id="tags" name="tags" value="{{ old('tags') }}" data-role="tagsinput">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Enter tuşu ile etiket ekleyin</small>
                                </div>
                            </div>

                            <!-- Özellikler -->
                            <div class="tab-pane fade" id="attributes" role="tabpanel">
                                <div id="attributes-container">
                                    <p class="text-muted">Kategori seçtikten sonra özellikler yüklenecektir.</p>
                                </div>
                            </div>

                            <!-- Resimler -->
                            <div class="tab-pane fade" id="images" role="tabpanel">
                                <div class="form-group">
                                    <label for="images">Ürün Resimleri</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('images') is-invalid @enderror" 
                                               id="images" name="images[]" accept="image/*" multiple>
                                        <label class="custom-file-label" for="images">Dosyaları Seç</label>
                                    </div>
                                    @error('images')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Birden fazla resim seçebilirsiniz. Desteklenen formatlar: JPG, PNG, GIF. Maksimum boyut: 2MB
                                    </small>
                                </div>

                                <div id="image-preview" class="row mt-3"></div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="approval_status">Onay Durumu <span class="text-danger">*</span></label>
                                    <select class="form-control @error('approval_status') is-invalid @enderror" 
                                            id="approval_status" name="approval_status" required>
                                        <option value="approved" {{ old('approval_status', 'approved') == 'approved' ? 'selected' : '' }}>Onaylı</option>
                                        <option value="pending" {{ old('approval_status') == 'pending' ? 'selected' : '' }}>Onay Bekliyor</option>
                                    </select>
                                    @error('approval_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
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
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-tagsinput@0.7.1/dist/bootstrap-tagsinput.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-tagsinput@0.7.1/dist/bootstrap-tagsinput.min.js"></script>
<script>
    $(document).ready(function() {
        // Select2 başlat
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // Summernote başlat
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Dosya seçildiğinde dosya adını göster
        $('.custom-file-input').on('change', function() {
            let files = $(this)[0].files;
            let label = $(this).next('.custom-file-label');
            
            if (files.length > 1) {
                label.html(files.length + ' dosya seçildi');
            } else if (files.length === 1) {
                label.html(files[0].name);
            }

            // Resim önizleme
            $('#image-preview').empty();
            for (let i = 0; i < files.length; i++) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').append(`
                        <div class="col-md-3 mb-3">
                            <img src="${e.target.result}" class="img-fluid img-thumbnail">
                        </div>
                    `);
                }
                reader.readAsDataURL(files[i]);
            }
        });

        // Kategori değiştiğinde özellikleri yükle
        $('#category_id').on('change', function() {
            let categoryId = $(this).val();
            if (categoryId) {
                $.get(`/admin/categories/${categoryId}/attributes`, function(data) {
                    $('#attributes-container').html(data);
                });
            } else {
                $('#attributes-container').html('<p class="text-muted">Kategori seçtikten sonra özellikler yüklenecektir.</p>');
            }
        });
    });
</script>
@endpush
