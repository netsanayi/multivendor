@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adres Detayı: {{ $address->address_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.addresses.edit', [$user, $address]) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.users.addresses.index', $user) }}" class="btn btn-secondary btn-sm">
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
                                        <a href="{{ route('admin.users.show', $user) }}">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </a>
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
                            </table>

                            <h5 class="mt-4">Detaylı Adres</h5>
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
                                    <th>Sokak</th>
                                    <td>{{ $address->street }}</td>
                                </tr>
                                @if($address->road_name)
                                <tr>
                                    <th>Yol Adı</th>
                                    <td>{{ $address->road_name }}</td>
                                </tr>
                                @endif
                                @if($address->building_no)
                                <tr>
                                    <th>Bina No</th>
                                    <td>{{ $address->building_no }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Kapı No</th>
                                    <td>{{ $address->door_no }}</td>
                                </tr>
                                @if($address->floor)
                                <tr>
                                    <th>Kat</th>
                                    <td>{{ $address->floor }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($address->company_type == 'corporate')
                            <h5>Kurumsal Bilgiler</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Şirket Adı</th>
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
                            @else
                            <h5>Bireysel Bilgiler</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">TC Kimlik No</th>
                                    <td>{{ $address->tc_id_no }}</td>
                                </tr>
                            </table>
                            @endif

                            <h5 class="mt-4">Sistem Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Oluşturulma Tarihi</th>
                                    <td>{{ $address->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $address->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Tam Adres</h6>
                                    <address>
                                        {{ $address->street }} {{ $address->road_name }}<br>
                                        No: {{ $address->building_no }}{{ $address->door_no ? '/' . $address->door_no : '' }}
                                        {{ $address->floor ? 'Kat: ' . $address->floor : '' }}<br>
                                        {{ $address->district }} / {{ $address->city }}<br>
                                        @if($address->company_type == 'corporate')
                                            <br>
                                            <strong>{{ $address->company_name }}</strong><br>
                                            Vergi Dairesi: {{ $address->tax_office }}<br>
                                            Vergi No: {{ $address->tax_no }}
                                        @endif
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
