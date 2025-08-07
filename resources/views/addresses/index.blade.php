@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $user->first_name }} {{ $user->last_name }} - Adresler</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.addresses.create', $user) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Adres Ekle
                        </a>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kullanıcıya Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($addresses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Adres Adı</th>
                                    <th>Adres</th>
                                    <th>Tür</th>
                                    <th width="100">Durum</th>
                                    <th width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($addresses as $address)
                                <tr>
                                    <td>{{ $address->id }}</td>
                                    <td>{{ $address->address_name }}</td>
                                    <td>
                                        {{ $address->street }} {{ $address->road_name }}<br>
                                        No: {{ $address->building_no }}{{ $address->door_no ? '/' . $address->door_no : '' }}
                                        {{ $address->floor ? 'Kat: ' . $address->floor : '' }}<br>
                                        {{ $address->district }} / {{ $address->city }}
                                    </td>
                                    <td>
                                        @if($address->company_type == 'corporate')
                                            <span class="badge badge-primary">Kurumsal</span>
                                            <br><small>{{ $address->company_name }}</small>
                                        @else
                                            <span class="badge badge-secondary">Bireysel</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($address->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.addresses.show', [$user, $address]) }}" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.addresses.edit', [$user, $address]) }}" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.addresses.destroy', [$user, $address]) }}" 
                                              method="POST" class="d-inline-block" 
                                              onsubmit="return confirm('Bu adresi silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Bu kullanıcının henüz adresi bulunmuyor.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Başarı ve hata mesajlarını göster
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
</script>
@endpush
