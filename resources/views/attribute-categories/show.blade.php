@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Özellik Kategorisi Detayı: {{ $attributeCategory->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.attribute-categories.edit', $attributeCategory) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.attribute-categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Kategori Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="120">ID</th>
                                            <td>{{ $attributeCategory->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori Adı</th>
                                            <td>{{ $attributeCategory->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>İkon</th>
                                            <td>
                                                @if($attributeCategory->image)
                                                    <img src="{{ $attributeCategory->image->url }}" alt="{{ $attributeCategory->name }}" 
                                                         class="img-thumbnail" style="max-width: 64px;">
                                                @else
                                                    <span class="text-muted">İkon yok</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Durum</th>
                                            <td>
                                                @if($attributeCategory->status)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Pasif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Özellik Sayısı</th>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $attributeCategory->productAttributes->count() }} özellik
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Oluşturulma</th>
                                            <td>{{ $attributeCategory->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Güncelleme</th>
                                            <td>{{ $attributeCategory->updated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Bu Kategorideki Özellikler</h5>
                                </div>
                                <div class="card-body">
                                    @if($attributeCategory->productAttributes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="50">ID</th>
                                                        <th>Özellik Adı</th>
                                                        <th width="150">Tip</th>
                                                        <th width="100">Durum</th>
                                                        <th width="120">İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($attributeCategory->productAttributes as $attribute)
                                                    <tr>
                                                        <td>{{ $attribute->id }}</td>
                                                        <td>{{ $attribute->name }}</td>
                                                        <td>
                                                            @switch($attribute->type)
                                                                @case('select')
                                                                    <span class="badge badge-info">Seçim Listesi</span>
                                                                    @break
                                                                @case('multi_select')
                                                                    <span class="badge badge-warning">Çoklu Seçim</span>
                                                                    @break
                                                                @case('text')
                                                                    <span class="badge badge-secondary">Metin</span>
                                                                    @break
                                                                @case('number')
                                                                    <span class="badge badge-primary">Sayı</span>
                                                                    @break
                                                                @case('date')
                                                                    <span class="badge badge-dark">Tarih</span>
                                                                    @break
                                                                @case('boolean')
                                                                    <span class="badge badge-success">Evet/Hayır</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge badge-light">{{ $attribute->type }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            @if($attribute->status)
                                                                <span class="badge badge-success">Aktif</span>
                                                            @else
                                                                <span class="badge badge-danger">Pasif</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.product-attributes.show', $attribute) }}" 
                                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.product-attributes.edit', $attribute) }}" 
                                                               class="btn btn-warning btn-sm" title="Düzenle">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i> Bu kategoride henüz özellik tanımlanmamış.
                                            <a href="{{ route('admin.product-attributes.create') }}" class="alert-link">
                                                Yeni özellik ekle
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Kullanım Örneği</h5>
                                </div>
                                <div class="card-body">
                                    <p>Bu kategori, ürünlere özellik eklerken kullanılacak özellikleri gruplar. Örneğin:</p>
                                    <div class="alert alert-light">
                                        <strong>{{ $attributeCategory->name }}</strong> kategorisi altındaki özellikler:
                                        <ul class="mb-0 mt-2">
                                            @if($attributeCategory->productAttributes->count() > 0)
                                                @foreach($attributeCategory->productAttributes->take(5) as $attribute)
                                                    <li>{{ $attribute->name }}</li>
                                                @endforeach
                                                @if($attributeCategory->productAttributes->count() > 5)
                                                    <li>...</li>
                                                @endif
                                            @else
                                                <li class="text-muted">Henüz özellik eklenmemiş</li>
                                            @endif
                                        </ul>
                                    </div>
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
