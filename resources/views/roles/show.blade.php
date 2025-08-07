@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rol Detayı: {{ $role->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">ID</th>
                                    <td>{{ $role->id }}</td>
                                </tr>
                                <tr>
                                    <th>Rol Adı</th>
                                    <td>
                                        {{ $role->name }}
                                        @if(in_array($role->name, ['Admin', 'Vendor', 'Customer']))
                                            <span class="badge badge-info ml-1">Sistem Rolü</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        @if($role->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kullanıcı Sayısı</th>
                                    <td>{{ $role->users->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma Tarihi</th>
                                    <td>{{ $role->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $role->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>İzinler</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Modül</th>
                                            <th class="text-center">Görüntüle</th>
                                            <th class="text-center">Oluştur</th>
                                            <th class="text-center">Düzenle</th>
                                            <th class="text-center">Sil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $currentPermissions = $role->permissions ?? [];
                                        @endphp
                                        @foreach($modules as $key => $module)
                                        <tr>
                                            <td><small>{{ $module['name'] }}</small></td>
                                            @foreach(['view', 'create', 'edit', 'delete'] as $permission)
                                            <td class="text-center">
                                                @if(in_array($permission, $module['permissions']))
                                                    @if(isset($currentPermissions[$key]) && in_array($permission, $currentPermissions[$key]))
                                                        <i class="fas fa-check text-success"></i>
                                                    @else
                                                        <i class="fas fa-times text-danger"></i>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($role->users->count() > 0)
                    <div class="mt-4">
                        <h5>Bu Role Sahip Kullanıcılar</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ad Soyad</th>
                                        <th>E-posta</th>
                                        <th>Durum</th>
                                        <th>Kayıt Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->status)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Görüntüle
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
