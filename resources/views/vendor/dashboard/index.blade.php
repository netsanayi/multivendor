@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <p class="mb-0">Hoş geldiniz, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('vendor.dashboard', ['period' => 'today']) }}" 
                   class="btn {{ $period == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">Bugün</a>
                <a href="{{ route('vendor.dashboard', ['period' => 'week']) }}" 
                   class="btn {{ $period == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Bu Hafta</a>
                <a href="{{ route('vendor.dashboard', ['period' => 'month']) }}" 
                   class="btn {{ $period == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Bu Ay</a>
                <a href="{{ route('vendor.dashboard', ['period' => 'year']) }}" 
                   class="btn {{ $period == 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Bu Yıl</a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Earnings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Toplam Kazanç
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₺{{ number_format($stats['total_earnings'], 2) }}
                            </div>
                            @if($stats['earnings_growth'] != 0)
                            <small class="{{ $stats['earnings_growth'] > 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $stats['earnings_growth'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ abs($stats['earnings_growth']) }}%
                            </small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lira-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Earnings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Bekleyen Ödeme
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₺{{ number_format($stats['pending_earnings'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aktif Ürünler
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_products'] }}
                            </div>
                            @if($stats['out_of_stock_products'] > 0)
                            <small class="text-danger">
                                {{ $stats['out_of_stock_products'] }} stokta yok
                            </small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Komisyon Oranı
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                %{{ $stats['commission_rate'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Earnings Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kazanç Grafiği</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">En Çok Satan Ürünler</h6>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $vendorProduct)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">{{ $vendorProduct->product->sku }}</small>
                                <div class="font-weight-bold">{{ Str::limit($vendorProduct->product->name, 30) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">₺{{ number_format($vendorProduct->price, 2) }}</div>
                                <small class="text-muted">Stok: {{ $vendorProduct->stock_quantity }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Henüz ürün bulunmuyor.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Pending Payouts -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Son Siparişler</h6>
                </div>
                <div class="card-body">
                    @forelse($recentOrders as $order)
                    <div class="mb-3 border-bottom pb-3">
                        <!-- Order details will be here when Orders module is created -->
                    </div>
                    @empty
                    <p class="text-muted text-center">Henüz sipariş bulunmuyor.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pending Payouts -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bekleyen Ödemeler</h6>
                </div>
                <div class="card-body">
                    @forelse($pendingPayouts as $payout)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">{{ $payout->payout_number }}</small>
                                <div class="font-weight-bold">₺{{ number_format($payout->amount, 2) }}</div>
                            </div>
                            <div>
                                <span class="badge badge-{{ $payout->status_color }}">
                                    {{ $payout->status_label }}
                                </span>
                            </div>
                        </div>
                        <small class="text-muted">{{ $payout->requested_at->diffForHumans() }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center">Bekleyen ödeme bulunmuyor.</p>
                    @endforelse
                    
                    <div class="mt-3">
                        <a href="{{ route('vendor.payouts') }}" class="btn btn-sm btn-primary btn-block">
                            Tüm Ödemeler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Chart
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: @json($chartData['datasets'])
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₺' + value.toLocaleString('tr-TR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '₺' + context.parsed.y.toLocaleString('tr-TR');
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
