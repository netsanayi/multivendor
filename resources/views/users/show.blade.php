@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Detayı: {{ $user->first_name }} {{ $user->last_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.users.addresses.index', $user) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-map-marker-alt"></i> Adresler
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                                    </div>
                                    <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    <p>
                                        <span class="badge badge-info">{{ $user->role->name ?? 'Rol Atanmamış' }}</span>
                                        @if($user->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-link active" id="nav-info-tab" data-toggle="tab" href="#nav-info" role="tab">Genel Bilgiler</a>
                                    <a class="nav-link" id="nav-addresses-tab" data-toggle="tab" href="#nav-addresses" role="tab">Adresler</a>
                                    <a class="nav-link" id="nav-products-tab" data-toggle="tab" href="#nav-products" role="tab">Ürünler</a>
                                    <a class="nav-link" id="nav-activity-tab" data-toggle="tab" href="#nav-activity" role="tab">Aktiviteler</a>
                                </div>
                            </nav>
                            <div class="tab-content mt-3" id="nav-tabContent">
                                <!-- Genel Bilgiler -->
                                <div class="tab-pane fade show active" id="nav-info" role="tabpanel">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="200">ID</th>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ad Soyad</th>
                                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>E-posta</th>
                                            <td>
                                                {{ $user->email }}
                                                @if($user->email_verified_at)
                                                    <i class="fas fa-check-circle text-success" title="Doğrulanmış"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-danger" title="Doğrulanmamış"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Telefon</th>
                                            <td>{{ $user->phone_number ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rol</th>
                                            <td><span class="badge badge-info">{{ $user->role->name ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Varsayılan Para Birimi</th>
                                            <td>{{ $user->defaultCurrency ? $user->defaultCurrency->name . ' (' . $user->defaultCurrency->symbol . ')' : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>2FA Durumu</th>
                                            <td>
                                                @if($user->two_factor_secret)
                                                    <span class="text-success">Aktif</span>
                                                @else
                                                    <span class="text-muted">Pasif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kayıt Tarihi</th>
                                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Son Güncelleme</th>
                                            <td>{{ $user->updated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Adresler -->
                                <div class="tab-pane fade" id="nav-addresses" role="tabpanel">
                                    @if($user->addresses->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Adres Adı</th>
                                                        <th>Adres</th>
                                                        <th>Durum</th>
                                                        <th width="100">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->addresses as $address)
                                                    <tr>
                                                        <td>{{ $address->address_name }}</td>
                                                        <td>
                                                            {{ $address->street }} {{ $address->road_name }}<br>
                                                            No: {{ $address->building_no }}{{ $address->door_no ? '/' . $address->door_no : '' }}
                                                            {{ $address->floor ? 'Kat: ' . $address->floor : '' }}<br>
                                                            {{ $address->district }} / {{ $address->city }}
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
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Henüz adres eklenmemiş.</p>
                                    @endif
                                    <a href="{{ route('admin.users.addresses.create', $user) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Yeni Adres Ekle
                                    </a>
                                </div>

                                <!-- Ürünler -->
                                <div class="tab-pane fade" id="nav-products" role="tabpanel">
                                    @if($user->vendorProducts->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Ürün</th>
                                                        <th>Fiyat</th>
                                                        <th>Stok</th>
                                                        <th>Durum</th>
                                                        <th width="100">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->vendorProducts as $vendorProduct)
                                                    <tr>
                                                        <td>{{ $vendorProduct->product->name ?? '-' }}</td>
                                                        <td>{{ number_format($vendorProduct->price, 2) }} {{ $vendorProduct->currency->symbol ?? 'TL' }}</td>
                                                        <td>{{ $vendorProduct->stock_quantity }}</td>
                                                        <td>
                                                            @if($vendorProduct->status)
                                                                <span class="badge badge-success">Aktif</span>
                                                            @else
                                                                <span class="badge badge-danger">Pasif</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.vendor-products.show', $vendorProduct) }}" 
                                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Kullanıcının henüz ürünü bulunmuyor.</p>
                                    @endif
                                </div>

                                <!-- Aktiviteler -->
                                <div class="tab-pane fade" id="nav-activity" role="tabpanel">
                                    <p class="text-muted">Son aktiviteler burada görüntülenecek.</p>
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
