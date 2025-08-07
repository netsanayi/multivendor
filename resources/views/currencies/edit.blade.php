@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Para Birimi Düzenle: {{ $currency->name }}</h3>
                </div>
                <form action="{{ route('admin.currencies.update', $currency) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Para Birimi Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $currency->name) }}" 
                                           placeholder="Örn: Türk Lirası, Amerikan Doları" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="symbol">Sembol <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('symbol') is-invalid @enderror" 
                                           id="symbol" name="symbol" value="{{ old('symbol', $currency->symbol) }}" 
                                           placeholder="Örn: ₺, $, €" required maxlength="10">
                                    @error('symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Sembol Pozisyonu <span class="text-danger">*</span></label>
                                    <select class="form-control @error('position') is-invalid @enderror" 
                                            id="position" name="position" required>
                                        <option value="left" {{ old('position', $currency->position) == 'left' ? 'selected' : '' }}>Sol</option>
                                        <option value="right" {{ old('position', $currency->position) == 'right' ? 'selected' : '' }}>Sağ</option>
                                    </select>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required
                                            {{ $currency->id == 1 ? 'disabled' : '' }}>
                                        <option value="1" {{ old('status', $currency->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status', $currency->status) == 0 ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                    @if($currency->id == 1)
                                        <input type="hidden" name="status" value="{{ $currency->status }}">
                                        <small class="form-text text-muted">Varsayılan para birimi pasif yapılamaz</small>
                                    @endif
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($currency->id == 1)
                        <div class="alert alert-warning">
                            <i class="icon fas fa-exclamation-triangle"></i>
                            Bu varsayılan para birimidir. Bazı ayarlar değiştirilemez.
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Bilgi</h5>
                                    <ul class="mb-0">
                                        <li>Para birimi adı benzersiz olmalıdır.</li>
                                        <li>Sembol alanına para biriminin sembolünü girin (₺, $, € gibi).</li>
                                        <li>Sembol pozisyonu, fiyatların nasıl gösterileceğini belirler.</li>
                                        <li>Pasif durumdaki para birimleri sistemde kullanılamaz.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>Oluşturulma:</strong> {{ $currency->created_at->format('d.m.Y H:i') }}<br>
                                <strong>Son Güncelleme:</strong> {{ $currency->updated_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">
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
        // Sembol pozisyonu değiştiğinde örnek göster
        $('#position, #symbol').on('change keyup', function() {
            updateExample();
        });

        function updateExample() {
            let symbol = $('#symbol').val() || '₺';
            let position = $('#position').val();
            let example = '';

            if (position === 'left') {
                example = symbol + '100';
            } else {
                example = '100' + symbol;
            }

            $('#position option[value="' + position + '"]').text(
                position === 'left' ? 'Sol (' + example + ')' : 'Sağ (' + example + ')'
            );
        }

        // Sayfa yüklendiğinde örneği güncelle
        updateExample();
    });
</script>
@endpush
