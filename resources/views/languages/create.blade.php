@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Dil Ekle</h3>
                </div>
                <form action="{{ route('admin.languages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Dil Kodu <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" 
                                           placeholder="Örn: tr, en, de" required maxlength="10">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        ISO 639-1 dil kodunu kullanın (tr, en, de, fr vb.)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Dil Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Örn: Türkçe, English, Deutsch" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="locale">Locale <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('locale') is-invalid @enderror" 
                                           id="locale" name="locale" value="{{ old('locale') }}" 
                                           placeholder="Örn: tr_TR, en_US, de_DE" required>
                                    @error('locale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Sistem locale formatı (dil_ÜLKE)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Bayrak Resmi</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Dosya Seç</label>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Önerilen boyut: 32x32 veya 64x64 piksel
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="order">Sıralama <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           id="order" name="order" value="{{ old('order', 0) }}" 
                                           min="0" required>
                                    @error('order')
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

                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Bilgi</h5>
                            <ul class="mb-0">
                                <li>Dil kodu benzersiz olmalıdır ve genellikle 2 harfli ISO 639-1 kodu kullanılır.</li>
                                <li>Locale, işletim sistemi ve PHP'nin kullandığı formattadır (ör: tr_TR).</li>
                                <li>Yeni dil eklendiğinde, varsayılan dil dosyaları otomatik olarak kopyalanacaktır.</li>
                                <li>Bayrak resmi, dil seçici menüde görüntülenecektir.</li>
                                <li>Pasif durumdaki diller sitede görüntülenmez.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
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
        // Dosya seçildiğinde dosya adını göster
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName);
        });

        // Dil kodu girildiğinde otomatik locale önerisi
        $('#code').on('input', function() {
            let code = $(this).val().toLowerCase();
            let localeInput = $('#locale');
            
            if (localeInput.val() === '') {
                let suggestions = {
                    'tr': 'tr_TR',
                    'en': 'en_US',
                    'de': 'de_DE',
                    'fr': 'fr_FR',
                    'es': 'es_ES',
                    'it': 'it_IT',
                    'ru': 'ru_RU',
                    'ar': 'ar_SA',
                    'zh': 'zh_CN',
                    'ja': 'ja_JP'
                };
                
                if (suggestions[code]) {
                    localeInput.val(suggestions[code]);
                }
            }
        });
    });
</script>
@endpush
