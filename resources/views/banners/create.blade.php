@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Banner Ekle</h3>
                </div>
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Banner Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Örn: Ana Sayfa Slider, Kampanya Banner" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link">Link</label>
                                    <input type="url" class="form-control @error('link') is-invalid @enderror" 
                                           id="link" name="link" value="{{ old('link') }}" 
                                           placeholder="https://example.com/kampanya">
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Banner'a tıklandığında yönlendirilecek URL (isteğe bağlı)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Banner Resmi <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*" required>
                                        <label class="custom-file-label" for="image">Dosya Seç</label>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Önerilen boyut: 1920x600 piksel. Maksimum dosya boyutu: 2MB
                                    </small>
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
                            <div class="col-md-12">
                                <div id="image-preview" class="mb-3" style="display: none;">
                                    <label>Önizleme:</label>
                                    <img src="" alt="Önizleme" class="img-fluid img-thumbnail" style="max-height: 300px;">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Banner Kullanım Bilgileri</h5>
                            <ul class="mb-0">
                                <li>Banner'lar genellikle ana sayfa veya kategori sayfalarında slider olarak kullanılır.</li>
                                <li>Yüksek kaliteli görseller kullanmaya özen gösterin.</li>
                                <li>Mobil uyumlu görseller tercih edin.</li>
                                <li>Link eklerseniz, banner'a tıklandığında belirtilen adrese yönlendirilir.</li>
                                <li>Pasif durumdaki banner'lar sitede görüntülenmez.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
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
