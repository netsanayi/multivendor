@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $user->first_name }} {{ $user->last_name }} - Adres Düzenle</h3>
                </div>
                <form action="{{ route('admin.users.addresses.update', [$user, $address]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_name">Adres Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address_name') is-invalid @enderror" 
                                           id="address_name" name="address_name" value="{{ old('address_name', $address->address_name) }}" 
                                           placeholder="Örn: Ev, İş" required>
                                    @error('address_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_type">Adres Türü <span class="text-danger">*</span></label>
                                    <select class="form-control @error('company_type') is-invalid @enderror" 
                                            id="company_type" name="company_type" required>
                                        <option value="individual" {{ old('company_type', $address->company_type) == 'individual' ? 'selected' : '' }}>Bireysel</option>
                                        <option value="corporate" {{ old('company_type', $address->company_type) == 'corporate' ? 'selected' : '' }}>Kurumsal</option>
                                    </select>
                                    @error('company_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">İl <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $address->city) }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="district">İlçe <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                           id="district" name="district" value="{{ old('district', $address->district) }}" required>
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street">Sokak <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                           id="street" name="street" value="{{ old('street', $address->street) }}" required>
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="road_name">Yol Adı</label>
                                    <input type="text" class="form-control @error('road_name') is-invalid @enderror" 
                                           id="road_name" name="road_name" value="{{ old('road_name', $address->road_name) }}">
                                    @error('road_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="building_no">Bina No</label>
                                    <input type="text" class="form-control @error('building_no') is-invalid @enderror" 
                                           id="building_no" name="building_no" value="{{ old('building_no', $address->building_no) }}">
                                    @error('building_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="door_no">Kapı No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('door_no') is-invalid @enderror" 
                                           id="door_no" name="door_no" value="{{ old('door_no', $address->door_no) }}" required>
                                    @error('door_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floor">Kat</label>
                                    <input type="text" class="form-control @error('floor') is-invalid @enderror" 
                                           id="floor" name="floor" value="{{ old('floor', $address->floor) }}">
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="1" {{ old('status', $address->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status', $address->status) == 0 ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Kurumsal Bilgiler -->
                        <div id="corporate-info" style="display: none;">
                            <h5 class="mt-4 mb-3">Kurumsal Bilgiler</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Şirket Adı <span class="required-corporate text-danger">*</span></label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name', $address->company_name) }}">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_office">Vergi Dairesi <span class="required-corporate text-danger">*</span></label>
                                        <input type="text" class="form-control @error('tax_office') is-invalid @enderror" 
                                               id="tax_office" name="tax_office" value="{{ old('tax_office', $address->tax_office) }}">
                                        @error('tax_office')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_no">Vergi No <span class="required-corporate text-danger">*</span></label>
                                        <input type="text" class="form-control @error('tax_no') is-invalid @enderror" 
                                               id="tax_no" name="tax_no" value="{{ old('tax_no', $address->tax_no) }}">
                                        @error('tax_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bireysel Bilgiler -->
                        <div id="individual-info">
                            <h5 class="mt-4 mb-3">Bireysel Bilgiler</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tc_id_no">TC Kimlik No <span class="required-individual text-danger">*</span></label>
                                        <input type="text" class="form-control @error('tc_id_no') is-invalid @enderror" 
                                               id="tc_id_no" name="tc_id_no" value="{{ old('tc_id_no', $address->tc_id_no) }}" 
                                               maxlength="11" pattern="[0-9]{11}">
                                        @error('tc_id_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">11 haneli TC kimlik numaranızı giriniz</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <strong>Oluşturulma:</strong> {{ $address->created_at->format('d.m.Y H:i') }}<br>
                            <strong>Son Güncelleme:</strong> {{ $address->updated_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.users.addresses.index', $user) }}" class="btn btn-secondary">
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
        // Adres türü değişimi
        $('#company_type').on('change', function() {
            if ($(this).val() === 'corporate') {
                $('#corporate-info').show();
                $('#individual-info').hide();
                // Kurumsal alanları zorunlu yap
                $('#company_name, #tax_office, #tax_no').prop('required', true);
                $('#tc_id_no').prop('required', false);
            } else {
                $('#corporate-info').hide();
                $('#individual-info').show();
                // Bireysel alanları zorunlu yap
                $('#tc_id_no').prop('required', true);
                $('#company_name, #tax_office, #tax_no').prop('required', false);
            }
        });

        // Sayfa yüklendiğinde kontrol et
        $('#company_type').trigger('change');

        // TC Kimlik No sadece sayı
        $('#tc_id_no').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endpush
