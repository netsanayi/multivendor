@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Rol Ekle</h3>
                </div>
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Rol Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
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

                        <h5 class="mt-4 mb-3">İzinler</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Modül</th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="view"> Görüntüle
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="create"> Oluştur
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="edit"> Düzenle
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="delete"> Sil
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $key => $module)
                                    <tr>
                                        <td>
                                            <strong>{{ $module['name'] }}</strong>
                                            <input type="checkbox" class="check-module ml-2" data-module="{{ $key }}">
                                        </td>
                                        @foreach($module['permissions'] as $permission)
                                        <td class="text-center">
                                            @if(in_array($permission, ['view', 'create', 'edit', 'delete']))
                                                <input type="checkbox" 
                                                       name="permissions[{{ $key }}][]" 
                                                       value="{{ $permission }}"
                                                       data-module="{{ $key }}"
                                                       data-action="{{ $permission }}"
                                                       class="permission-check"
                                                       {{ is_array(old("permissions.$key")) && in_array($permission, old("permissions.$key")) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                        @endforeach
                                        @if(count($module['permissions']) < 4)
                                            @for($i = count($module['permissions']); $i < 4; $i++)
                                                <td class="text-center">-</td>
                                            @endfor
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('permissions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
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
        // Tümünü seç checkboxları
        $('.check-all').on('change', function() {
            var action = $(this).data('action');
            var isChecked = $(this).is(':checked');
            
            $('.permission-check[data-action="' + action + '"]').prop('checked', isChecked);
        });

        // Modül checkboxları
        $('.check-module').on('change', function() {
            var module = $(this).data('module');
            var isChecked = $(this).is(':checked');
            
            $('.permission-check[data-module="' + module + '"]').prop('checked', isChecked);
        });

        // İzin checkbox değişimi
        $('.permission-check').on('change', function() {
            updateCheckAllStates();
            updateModuleCheckStates();
        });

        // Sayfa yüklendiğinde durumları güncelle
        updateCheckAllStates();
        updateModuleCheckStates();

        function updateCheckAllStates() {
            ['view', 'create', 'edit', 'delete'].forEach(function(action) {
                var total = $('.permission-check[data-action="' + action + '"]').length;
                var checked = $('.permission-check[data-action="' + action + '"]:checked').length;
                
                if (checked === 0) {
                    $('.check-all[data-action="' + action + '"]').prop('checked', false).prop('indeterminate', false);
                } else if (checked === total) {
                    $('.check-all[data-action="' + action + '"]').prop('checked', true).prop('indeterminate', false);
                } else {
                    $('.check-all[data-action="' + action + '"]').prop('checked', false).prop('indeterminate', true);
                }
            });
        }

        function updateModuleCheckStates() {
            $('.check-module').each(function() {
                var module = $(this).data('module');
                var total = $('.permission-check[data-module="' + module + '"]').length;
                var checked = $('.permission-check[data-module="' + module + '"]:checked').length;
                
                if (checked === 0) {
                    $(this).prop('checked', false).prop('indeterminate', false);
                } else if (checked === total) {
                    $(this).prop('checked', true).prop('indeterminate', false);
                } else {
                    $(this).prop('checked', false).prop('indeterminate', true);
                }
            });
        }
    });
</script>
@endpush
