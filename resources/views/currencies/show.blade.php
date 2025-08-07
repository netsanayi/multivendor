@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Para Birimi Detayı: {{ $currency->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary btn-sm">
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
                                    <td>
                                        {{ $currency->id }}
                                        @if($currency->id == 1)
                                            <span class="badge badge-info ml-1">Varsayılan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Para Birimi Adı</th>
                                    <td>{{ $currency->name }}</td>
                                </tr>
                                <tr>
                                    <th>Sembol</th>
                                    <td style="font-size: 1.5em;">{{ $currency->symbol }}</td>
                                </tr>
                                <tr>
                                    <th>Pozisyon</th>
                                    <td>
                                        @if($currency->position == 'left')
                                            Sol ({{ $currency->symbol }}100)
                                        @else
                                            Sağ (100{{ $currency->symbol }})
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        @if($currency->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma Tarihi</th>
                                    <td>{{ $currency->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Güncellenme Tarihi</th>
                                    <td>{{ $currency->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Kullanım İstatistikleri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Ürün Sayısı</th>
                                    <td>{{ $currency->products_count ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Müşteri Ürün Sayısı</th>
                                    <td>{{ $currency->vendor_products_count ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Kullanıcı Sayısı</th>
                                    <td>{{ $currency->users_count ?? 0 }}</td>
                                </tr>
                            </table>

                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Fiyat Örnekleri</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Küçük Değer:</strong><br>
                                        @if($currency->position == 'left')
                                            {{ $currency->symbol }}9,99
                                        @else
                                            9,99{{ $currency->symbol }}
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <strong>Büyük Değer:</strong><br>
                                        @if($currency->position == 'left')
                                            {{ $currency->symbol }}1.234.567,89
                                        @else
                                            1.234.567,89{{ $currency->symbol }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($currency->id == 1)
                            <div class="alert alert-warning">
                                <i class="icon fas fa-exclamation-triangle"></i>
                                Bu varsayılan para birimidir ve silinemez. Sistemde en az bir aktif para birimi bulunmalıdır.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
