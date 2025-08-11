@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Banner Detayı: {{ $banner->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Banner Görseli</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($banner->image)
                                        <img src="{{ $banner->image->url }}" alt="{{ $banner->name }}" 
                                             class="img-fluid" style="max-height: 400px;">
                                    @else
                                        <div class="text-muted py-5">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p>Görsel bulunamadı</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Banner Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th>ID</th>
                                            <td>{{ $banner->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Banner Adı</th>
                                            <td>{{ $banner->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Durum</th>
                                            <td>
                                                @if($banner->status)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Pasif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Link</th>
                                            <td>
                                                @if($banner->link)
                                                    <a href="{{ $banner->link }}" target="_blank" class="text-truncate d-block">
                                                        {{ $banner->link }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Link yok</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Oluşturulma</th>
                                            <td>{{ $banner->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Güncelleme</th>
                                            <td>{{ $banner->updated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    </table>

                                    @if($banner->image)
                                    <h6 class="mt-3">Resim Bilgileri</h6>
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th>Dosya Adı</th>
                                            <td class="text-truncate">{{ $banner->image->file_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Orijinal Ad</th>
                                            <td class="text-truncate">{{ $banner->image->name }}</td>
                                        </tr>
                                    </table>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Hızlı İşlemler</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-block mb-2 toggle-status"
                                                data-id="{{ $banner->id }}"
                                                data-status="{{ $banner->status }}">
                                            @if($banner->status)
                                                <i class="fas fa-toggle-off"></i> Pasif Yap
                                            @else
                                                <i class="fas fa-toggle-on"></i> Aktif Yap
                                            @endif
                                        </button>
                                        
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Bu banner\'ı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block">
                                                <i class="fas fa-trash"></i> Banner'ı Sil
                                            </button>
                                        </form>
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

    // Durum değiştirme
    $('.toggle-status').on('click', function() {
        let btn = $(this);
        let bannerId = btn.data('id');
        
        $.ajax({
            url: '/admin/banners/' + bannerId + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Buton metnini ve ikonu güncelle
                    if (response.status) {
                        btn.html('<i class="fas fa-toggle-off"></i> Pasif Yap');
                        $('.badge').removeClass('badge-danger').addClass('badge-success').text('Aktif');
                    } else {
                        btn.html('<i class="fas fa-toggle-on"></i> Aktif Yap');
                        $('.badge').removeClass('badge-success').addClass('badge-danger').text('Pasif');
                    }
                    
                    btn.data('status', response.status);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Durum güncellenirken bir hata oluştu.');
            }
        });
    });
</script>
@endpush
