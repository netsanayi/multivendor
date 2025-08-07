@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Özellik Kategorisi Ekle</h3>
                </div>
                <form action="{{ route('admin.attribute-categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Kategori Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
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
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Pasif</option>
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
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Dosya Seç</label>
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
                                    <label>Önizleme:</label><br>
                                    <img src="" alt="Önizleme" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h5><i class="icon fas fa-info"></i> Özellik Kategorisi Nedir?</h5>
                            <p>Özellik kategorileri, ürün özelliklerini gruplamak için kullanılır. Örneğin:</p>
                            <ul class="mb-0">
                                <li><strong>Teknik Özellikler:</strong> İşlemci, RAM, Ekran Boyutu vb.</li>
                                <li><strong>Fiziksel Özellikler:</strong> Renk, Boyut, Ağırlık vb.</li>
                                <li><strong>Genel Özellikler:</strong> Marka, Model, Garanti Süresi vb.</li>
                            </ul>
                            <p class="mb-0 mt-2">Her kategori altında birden fazla özellik tanımlayabilirsiniz.</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
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
