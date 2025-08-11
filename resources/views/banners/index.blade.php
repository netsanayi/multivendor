@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bannerlar</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Banner Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme -->
                    <form method="GET" action="{{ route('admin.banners.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Banner adı ara..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Ara
                                </button>
                                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bannerlar Grid -->
                    <div class="row">
                        @forelse($banners as $banner)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                @if($banner->image)
                                    <img src="{{ $banner->image->url }}" class="card-img-top" alt="{{ $banner->name }}" 
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $banner->name }}</h5>
                                    @if($banner->link)
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-link"></i> {{ Str::limit($banner->link, 30) }}
                                            </small>
                                        </p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($banner->status)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Pasif</span>
                                            @endif
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.banners.show', $banner) }}" 
                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.banners.edit', $banner) }}" 
                                               class="btn btn-warning btn-sm" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm toggle-status"
                                                    data-id="{{ $banner->id }}"
                                                    data-status="{{ $banner->status }}"
                                                    title="Durumu Değiştir">
                                                @if($banner->status)
                                                    <i class="fas fa-toggle-on text-success"></i>
                                                @else
                                                    <i class="fas fa-toggle-off text-danger"></i>
                                                @endif
                                            </button>
                                            <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                                  method="POST" class="d-inline-block" 
                                                  onsubmit="return confirm('Bu banner\'ı silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>{{ $banner->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Henüz banner eklenmemiş.
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-3">
                        {{ $banners->withQueryString()->links() }}
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
                    
                    // İkonu güncelle
                    if (response.status) {
                        btn.html('<i class="fas fa-toggle-on text-success"></i>');
                        btn.closest('.card').find('.badge').removeClass('badge-danger').addClass('badge-success').text('Aktif');
                    } else {
                        btn.html('<i class="fas fa-toggle-off text-danger"></i>');
                        btn.closest('.card').find('.badge').removeClass('badge-success').addClass('badge-danger').text('Pasif');
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
