@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adres Düzenle</h3>
                </div>
                <form action="{{ route('admin.addresses.update', $address) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            @if(auth()->user()->can('addresses.update'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Kullanıcı</label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" 
                                            id="user_id" name="user_id">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $address->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="user_id" value="{{ $address->user_id }}">
                            @endif
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
                        </div>

                        <div class="row">
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
                            <div class="col-md-6">
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
                                    <label for="street">Mahalle <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                           id="street" name="street" value="{{ old('street', $address->street) }}" required>
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="road_name">Sokak/Cadde</label>
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
                        </div>

                        <!-- Bireysel Bilgiler -->
                        <div id="individual-info" style="display: {{ old('company_type', $address->company_type) == 'individual' ? 'block' : 'none' }};">
                            <hr>
                            <h5>Bireysel Bilgiler</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tc_id_no">TC Kimlik No</label>
                                        <input type="text" class="form-control @error('tc_id_no') is-invalid @enderror" 
                                               id="tc_id_no" name="tc_id_no" value="{{ old('tc_id_no', $address->tc_id_no) }}" 
                                               maxlength="11" pattern="[0-9]{11}" placeholder="11 haneli TC Kimlik No">
                                        @error('tc_id_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kurumsal Bilgiler -->
                        <div id="corporate-info" style="display: {{ old('company_type', $address->company_type) == 'corporate' ? 'block' : 'none' }};">
                            <hr>
                            <h5>Kurumsal Bilgiler</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Firma Adı</label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name', $address->company_name) }}">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_office">Vergi Dairesi</label>
                                        <input type="text" class="form-control @error('tax_office') is-invalid @enderror" 
                                               id="tax_office" name="tax_office" value="{{ old('tax_office', $address->tax_office) }}">
                                        @error('tax_office')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_no">Vergi No</label>
                                        <input type="text" class="form-control @error('tax_no') is-invalid @enderror" 
                                               id="tax_no" name="tax_no" value="{{ old('tax_no', $address->tax_no) }}">
                                        @error('tax_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> İptal
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
        // Adres türü değiştiğinde ilgili alanları göster/gizle
        $('#company_type').on('change', function() {
            var type = $(this).val();
            if (type === 'corporate') {
                $('#corporate-info').show();
                $('#individual-info').hide();
                // Kurumsal için zorunlu alanları ekle
                $('#company_name').attr('required', true);
                $('#tax_office').attr('required', true);
                $('#tax_no').attr('required', true);
                $('#tc_id_no').removeAttr('required');
            } else {
                $('#corporate-info').hide();
                $('#individual-info').show();
                // Bireysel için zorunlu alanları ekle
                $('#tc_id_no').attr('required', true);
                $('#company_name').removeAttr('required');
                $('#tax_office').removeAttr('required');
                $('#tax_no').removeAttr('required');
            }
        });

        // Sayfa yüklendiğinde de kontrol et
        $('#company_type').trigger('change');
    });
</script>
@endpush
