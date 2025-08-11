@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adres Detayı: {{ $address->address_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.addresses.edit', $address) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Adres Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">ID</th>
                                    <td>{{ $address->id }}</td>
                                </tr>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <td>
                                        @if($address->user)
                                            <a href="{{ route('admin.users.show', $address->user) }}">
                                                {{ $address->user->first_name }} {{ $address->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Adres Adı</th>
                                    <td>{{ $address->address_name }}</td>
                                </tr>
                                <tr>
                                    <th>Adres Türü</th>
                                    <td>
                                        @if($address->company_type == 'corporate')
                                            <span class="badge badge-primary">Kurumsal</span>
                                        @else
                                            <span class="badge badge-secondary">Bireysel</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        @if($address->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma Tarihi</th>
                                    <td>{{ $address->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $address->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Konum Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">İl</th>
                                    <td>{{ $address->city }}</td>
                                </tr>
                                <tr>
                                    <th>İlçe</th>
                                    <td>{{ $address->district }}</td>
                                </tr>
                                <tr>
                                    <th>Mahalle</th>
                                    <td>{{ $address->street }}</td>
                                </tr>
                                <tr>
                                    <th>Sokak/Cadde</th>
                                    <td>{{ $address->road_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Bina No</th>
                                    <td>{{ $address->building_no ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kapı No</th>
                                    <td>{{ $address->door_no }}</td>
                                </tr>
                                <tr>
                                    <th>Kat</th>
                                    <td>{{ $address->floor ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($address->company_type == 'corporate')
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Kurumsal Bilgiler</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Firma Adı</th>
                                    <td>{{ $address->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Vergi Dairesi</th>
                                    <td>{{ $address->tax_office }}</td>
                                </tr>
                                <tr>
                                    <th>Vergi No</th>
                                    <td>{{ $address->tax_no }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @elseif($address->company_type == 'individual')
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Bireysel Bilgiler</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">TC Kimlik No</th>
                                    <td>{{ $address->tc_id_no ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Tam Adres</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">
                                        {{ $address->street }} 
                                        {{ $address->road_name ? $address->road_name . ' ' : '' }}
                                        No: {{ $address->building_no }}{{ $address->door_no ? '/' . $address->door_no : '' }}
                                        {{ $address->floor ? 'Kat: ' . $address->floor : '' }}<br>
                                        {{ $address->district }} / {{ $address->city }}
                                    </p>
                                    @if($address->company_type == 'corporate' && $address->company_name)
                                        <hr>
                                        <p class="mb-0">
                                            <strong>Firma:</strong> {{ $address->company_name }}<br>
                                            <strong>Vergi Dairesi:</strong> {{ $address->tax_office }}<br>
                                            <strong>Vergi No:</strong> {{ $address->tax_no }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.addresses.edit', $address) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <form action="{{ route('admin.addresses.destroy', $address) }}" method="POST" class="d-inline-block" 
                          onsubmit="return confirm('Bu adresi silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sil
                        </button>
                    </form>
                    <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Listeye Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
