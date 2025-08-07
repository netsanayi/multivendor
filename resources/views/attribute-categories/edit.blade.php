@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Özellik Kategorisi Düzenle: {{ $attributeCategory->name }}</h3>
                </div>
                <form action="{{ route('admin.attribute-categories.update', $attributeCategory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Kategori Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $attributeCategory->name) }}" 
                                           placeholder="Örn: Teknik Özellikler, Boyut, Renk" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="1" {{ old('status', $attributeCategory->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status', $attributeCategory->status) == 0 ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">İkon/Resim</label>
                                    
                                    @if($attributeCategory->image)
                                    <div class="mb-2">
                                        <img src="{{ $attributeCategory->image->url }}" alt="{{ $attributeCategory->name }}" 
                                             class="img-thumbnail" style="max-width: 64px;">
                                        <p class="text-muted mb-0">Mevcut ikon</p>
                                    </div>
                                    @endif
                                    
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Yeni ikon seç</label>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Önerilen boyut: 64x64 piksel. Maksimum dosya boyutu: 2MB
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="image-preview" style="display: none;">
                                    <label>Yeni Önizleme:</label><br>
                                    <img src="" alt="Önizleme" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h5><i class="icon fas fa-info"></i> Bilgi</h5>
                            <p>Bu kategoriye ait <strong>{{ $attributeCategory->productAttributes->count() }}</strong> özellik bulunmaktadır.</p>
                            @if($attributeCategory->productAttributes->count() > 0)
                                <p class="mb-0">Kategori silinirse, bu özelliklerin kategori bağlantısı kaldırılacaktır.</p>
                            @endif
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>Oluşturulma:</strong> {{ $attributeCategory->created_at->format('d.m.Y H:i') }}<br>
                                <strong>Son Güncelleme:</strong> {{ $attributeCategory->updated_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.attribute-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Dosya seçildiğinde dosya adını göster ve önizle
        $('#image').on('change', function() {
            let file = this.files[0];
            
            if (file) {
                // Dosya adını göster
                $(this).next('.custom-file-label').addClass('selected').html(file.name);
                
                // Önizleme
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview img').attr('src', e.target.result);
                    $('#image-preview').show();
                }
                reader.readAsDataURL(file);
                
                // Dosya boyutu kontrolü
                if (file.size > 2097152) { // 2MB
                    toastr.warning('Dosya boyutu 2MB\'dan büyük. Lütfen daha küçük bir dosya seçin.');
                }
            }
        });
    });
</script>
@endpush
