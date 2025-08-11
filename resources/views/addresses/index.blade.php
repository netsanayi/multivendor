@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adresler</h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.addresses.index') }}" method="GET" class="form-inline">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right" 
                                       placeholder="Adres ara..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <a href="{{ route('admin.addresses.create') }}" class="btn btn-primary btn-sm float-right mr-2">
                        <i class="fas fa-plus"></i> Yeni Adres Ekle
                    </a>
                </div>
                <div class="card-body">
                    @if($addresses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Kullanıcı</th>
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
                                    <td>
                                        @if($address->user)
                                            <a href="{{ route('admin.users.show', $address->user) }}">
                                                {{ $address->user->first_name }} {{ $address->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
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
                                        <a href="{{ route('admin.addresses.show', $address) }}" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.addresses.edit', $address) }}" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.addresses.destroy', $address) }}" 
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
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $addresses->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Henüz adres bulunmuyor.
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
