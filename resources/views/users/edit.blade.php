@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Düzenle: {{ $user->first_name }} {{ $user->last_name }}</h3>
                </div>
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Kişisel Bilgiler</h5>
                                <div class="form-group">
                                    <label for="first_name">Ad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">E-posta <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Telefon Numarası</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                                           placeholder="05XX XXX XX XX">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Hesap Bilgileri</h5>
                                <div class="form-group">
                                    <label for="password">Yeni Şifre</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Değiştirmek istemiyorsanız boş bırakın (En az 8 karakter)</small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Yeni Şifre Tekrar</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>

                                <div class="form-group">
                                    <label for="role_id">Rol <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role_id') is-invalid @enderror" 
                                            id="role_id" name="role_id" required>
                                        <option value="">Rol Seçin</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="default_currency_id">Varsayılan Para Birimi</label>
                                    <select class="form-control @error('default_currency_id') is-invalid @enderror" 
                                            id="default_currency_id" name="default_currency_id">
                                        <option value="">Seçiniz</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('default_currency_id', $user->default_currency_id) == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name }} ({{ $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('default_currency_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required 
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                    @if($user->id === auth()->id())
                                        <input type="hidden" name="status" value="{{ $user->status }}">
                                        <small class="form-text text-muted">Kendi hesabınızın durumunu değiştiremezsiniz</small>
                                    @endif
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                                                <p class="mb-0"><strong>Son Güncelleme:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Email Doğrulama:</strong> 
                                                    @if($user->email_verified_at)
                                                        <span class="text-success">{{ $user->email_verified_at->format('d.m.Y H:i') }}</span>
                                                    @else
                                                        <span class="text-danger">Doğrulanmamış</span>
                                                    @endif
                                                </p>
                                                <p class="mb-0"><strong>2FA Durumu:</strong> 
                                                    @if($user->two_factor_secret)
                                                        <span class="text-success">Aktif</span>
                                                    @else
                                                        <span class="text-muted">Pasif</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
        // Telefon numarası maskesi
        $('#phone_number').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = '';
            
            if (value.length > 0) {
                if (value.length <= 4) {
                    formattedValue = value;
                } else if (value.length <= 7) {
                    formattedValue = value.slice(0, 4) + ' ' + value.slice(4);
                } else if (value.length <= 9) {
                    formattedValue = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
                } else {
                    formattedValue = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7, 9) + ' ' + value.slice(9, 11);
                }
            }
            
            $(this).val(formattedValue);
        });

        // Şifre güç göstergesi
        $('#password').on('input', function() {
            let password = $(this).val();
            let strength = 0;
            
            // Uzunluk kontrolü
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Karakter çeşitliliği kontrolü
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            // Güç göstergesi güncelleme
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Zayıf';
                strengthClass = 'text-danger';
            } else if (strength <= 4) {
                strengthText = 'Orta';
                strengthClass = 'text-warning';
            } else {
                strengthText = 'Güçlü';
                strengthClass = 'text-success';
            }
            
            if (password.length > 0) {
                $(this).siblings('.form-text').html('Değiştirmek istemiyorsanız boş bırakın (En az 8 karakter) - Şifre Gücü: <span class="' + strengthClass + '">' + strengthText + '</span>');
            } else {
                $(this).siblings('.form-text').html('Değiştirmek istemiyorsanız boş bırakın (En az 8 karakter)');
            }
        });
    });
</script>
@endpush
