@extends('layouts.vendor')

@section('title', 'Kazançlarım')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Kazançlarım</h1>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Toplam Brüt</h6>
                    <h4>₺{{ number_format($summary['total_gross'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Toplam Komisyon</h6>
                    <h4 class="text-danger">₺{{ number_format($summary['total_commission'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Toplam Net</h6>
                    <h4 class="text-success">₺{{ number_format($summary['total_net'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Beklemede</h6>
                    <h4 class="text-warning">₺{{ number_format($summary['pending_amount'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Onaylandı</h6>
                    <h4 class="text-info">₺{{ number_format($summary['approved_amount'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Ödendi</h6>
                    <h4 class="text-success">₺{{ number_format($summary['paid_amount'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtreler</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('vendor.earnings') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Başlangıç Tarihi</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Bitiş Tarihi</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Durum</label>
                        <select name="status" class="form-control">
                            <option value="">Tümü</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Onaylandı</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Ödendi</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>İade</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Filtrele</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Earnings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kazanç Listesi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Sipariş #</th>
                            <th>Brüt Tutar</th>
                            <th>Komisyon</th>
                            <th>Net Tutar</th>
                            <th>Durum</th>
                            <th>Ödeme Yöntemi</th>
                            <th>İşlem #</th>
                            <th>Notlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($earnings as $earning)
                        <tr>
                            <td>{{ $earning->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                @if($earning->order_id)
                                    #{{ $earning->order_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>₺{{ number_format($earning->gross_amount, 2) }}</td>
                            <td class="text-danger">₺{{ number_format($earning->commission_amount, 2) }}</td>
                            <td class="text-success">₺{{ number_format($earning->net_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $earning->status_color }}">
                                    {{ $earning->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($earning->payment_method)
                                    {{ ucfirst($earning->payment_method) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($earning->transaction_id)
                                    <small>{{ $earning->transaction_id }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($earning->notes)
                                    <small>{{ Str::limit($earning->notes, 50) }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Kazanç kaydı bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            {{ $earnings->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
