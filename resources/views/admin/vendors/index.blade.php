@extends('layouts.admin')

@section('title', 'Satıcılar')
@section('page-title', 'Satıcılar')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Satıcılar</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Satıcı Listesi</h5>
            <a href="{{ route('admin.vendors.applications') }}" class="btn btn-info">
                <i class="ki-duotone ki-time fs-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Bekleyen Başvurular
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Satıcı Adı</th>
                        <th>E-posta</th>
                        <th>Ürün Sayısı</th>
                        <th>Toplam Kazanç</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $vendor->name }}</div>
                                    @if($vendor->company_name)
                                        <small class="text-muted">{{ $vendor->company_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $vendor->email }}</td>
                        <td>
                            <span class="badge bg-info">{{ $vendor->statistics['total_products'] ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ number_format($vendor->statistics['total_earnings'] ?? 0, 2) }} TL</span>
                        </td>
                        <td>
                            @if($vendor->vendor_status == 'approved')
                                <span class="badge bg-success">Onaylı</span>
                            @elseif($vendor->vendor_status == 'pending')
                                <span class="badge bg-warning">Beklemede</span>
                            @elseif($vendor->vendor_status == 'suspended')
                                <span class="badge bg-danger">Askıda</span>
                            @else
                                <span class="badge bg-secondary">{{ $vendor->vendor_status }}</span>
                            @endif
                        </td>
                        <td>{{ $vendor->created_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.vendors.show', $vendor) }}" 
                                   class="btn btn-sm btn-light-primary" title="Detay">
                                    <i class="ki-duotone ki-eye fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </a>
                                @if($vendor->vendor_status == 'approved')
                                    <button type="button" class="btn btn-sm btn-light-warning" 
                                            title="Askıya Al"
                                            onclick="suspendVendor({{ $vendor->id }})">
                                        <i class="ki-duotone ki-lock fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                @elseif($vendor->vendor_status == 'suspended')
                                    <form action="{{ route('admin.vendors.activate', $vendor) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light-success" 
                                                title="Aktifleştir">
                                            <i class="ki-duotone ki-check fs-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="ki-duotone ki-shop fs-4x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <p class="text-muted">Henüz satıcı bulunmamaktadır.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $vendors->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="suspendForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Satıcıyı Askıya Al</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Askıya Alma Nedeni <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Süre (Gün)</label>
                        <input type="number" class="form-control" id="duration" name="duration" min="1">
                        <small class="text-muted">Boş bırakılırsa süresiz askıya alınır.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Askıya Al</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function suspendVendor(vendorId) {
    const modal = new bootstrap.Modal(document.getElementById('suspendModal'));
    document.getElementById('suspendForm').action = `/admin/vendors/${vendorId}/suspend`;
    modal.show();
}
</script>
@endpush
