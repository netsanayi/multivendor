@extends('layouts.app')

@section('title', 'Favorilerim')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Favorilerim</h1>
            <p class="text-muted">
                <i class="fas fa-heart text-danger"></i> 
                {{ $wishlists->total() }} ürün listeleniyor
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary" onclick="shareWishlist()">
                    <i class="fas fa-share"></i> Paylaş
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="clearWishlist()">
                    <i class="fas fa-trash"></i> Tümünü Temizle
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">{{ $summary['total_items'] }}</h5>
                    <p class="card-text text-muted">Toplam Ürün</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">₺{{ number_format($summary['total_value'], 2) }}</h5>
                    <p class="card-text text-muted">Toplam Değer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">{{ $summary['on_sale_count'] }}</h5>
                    <p class="card-text text-muted">İndirimde</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">₺{{ number_format($summary['total_savings'], 2) }}</h5>
                    <p class="card-text text-muted">Toplam Tasarruf</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sort Options -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Sırala:</span>
                    <div class="btn-group ml-2" role="group">
                        <a href="{{ route('wishlist.index', ['sort' => 'priority']) }}" 
                           class="btn btn-sm {{ $sortBy == 'priority' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Öncelik
                        </a>
                        <a href="{{ route('wishlist.index', ['sort' => 'date']) }}" 
                           class="btn btn-sm {{ $sortBy == 'date' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Tarih
                        </a>
                        <a href="{{ route('wishlist.index', ['sort' => 'name']) }}" 
                           class="btn btn-sm {{ $sortBy == 'name' ? 'btn-primary' : 'btn-outline-primary' }}">
                            İsim
                        </a>
                        <a href="{{ route('wishlist.index', ['sort' => 'price_low']) }}" 
                           class="btn btn-sm {{ $sortBy == 'price_low' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Fiyat ↑
                        </a>
                        <a href="{{ route('wishlist.index', ['sort' => 'price_high']) }}" 
                           class="btn btn-sm {{ $sortBy == 'price_high' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Fiyat ↓
                        </a>
                    </div>
                </div>
                <div>
                    <button class="btn btn-success" onclick="addAllToCart()">
                        <i class="fas fa-shopping-cart"></i> Tümünü Sepete Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Wishlist Items -->
    <div class="row">
        @forelse($wishlists as $wishlist)
        <div class="col-md-4 col-lg-3 mb-4" id="wishlist-item-{{ $wishlist->id }}">
            <div class="card h-100 wishlist-card">
                <!-- Sale Badge -->
                @if($wishlist->isOnSale())
                <div class="badge badge-danger position-absolute" style="top: 10px; right: 10px; z-index: 1;">
                    -{{ $wishlist->discount_percentage }}%
                </div>
                @endif

                <!-- Remove Button -->
                <button class="btn btn-sm btn-light position-absolute" 
                        style="top: 10px; left: 10px; z-index: 1;"
                        onclick="removeFromWishlist({{ $wishlist->id }})">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Product Image -->
                @if($wishlist->product->images && $wishlist->product->images->count() > 0)
                    <img src="{{ $wishlist->product->images->first()->url }}" 
                         class="card-img-top" 
                         alt="{{ $wishlist->product->name }}"
                         style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif

                <div class="card-body">
                    <!-- Priority Stars -->
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $wishlist->priority ? 'text-warning' : 'text-muted' }}" 
                               style="cursor: pointer; font-size: 12px;"
                               onclick="updatePriority({{ $wishlist->id }}, {{ $i }})"></i>
                        @endfor
                    </div>

                    <!-- Product Name -->
                    <h6 class="card-title">
                        <a href="{{ route('frontend.products.show', $wishlist->product->slug) }}" 
                           class="text-dark text-decoration-none">
                            {{ Str::limit($wishlist->product->name, 50) }}
                        </a>
                    </h6>

                    <!-- Category -->
                    @if($wishlist->product->category)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-folder"></i> {{ $wishlist->product->category->name }}
                    </p>
                    @endif

                    <!-- Price -->
                    <div class="mb-2">
                        @if($wishlist->isOnSale())
                            <span class="text-muted text-decoration-line-through">
                                ₺{{ number_format($wishlist->price_when_added, 2) }}
                            </span>
                            <span class="h5 text-danger ml-2">
                                ₺{{ number_format($wishlist->product->price, 2) }}
                            </span>
                        @else
                            <span class="h5">
                                ₺{{ number_format($wishlist->product->price, 2) }}
                            </span>
                        @endif
                    </div>

                    <!-- Notes -->
                    @if($wishlist->notes)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-sticky-note"></i> {{ Str::limit($wishlist->notes, 50) }}
                    </p>
                    @endif

                    <!-- Added Date -->
                    <p class="text-muted small mb-3">
                        <i class="fas fa-calendar"></i> {{ $wishlist->added_at->diffForHumans() }}
                    </p>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $wishlist->product->id }})">
                            <i class="fas fa-shopping-cart"></i> Sepete Ekle
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" 
                                onclick="editWishlistItem({{ $wishlist->id }})">
                            <i class="fas fa-edit"></i> Düzenle
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-heart fa-5x text-muted mb-3"></i>
                <h4>Favori listeniz boş</h4>
                <p class="text-muted">Beğendiğiniz ürünleri favorilerinize ekleyerek daha sonra kolayca ulaşabilirsiniz.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($wishlists->hasPages())
    <div class="row">
        <div class="col-12">
            {{ $wishlists->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editWishlistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Favori Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editWishlistForm">
                    <input type="hidden" id="edit_wishlist_id">
                    <div class="form-group">
                        <label>Öncelik</label>
                        <select class="form-control" id="edit_priority">
                            @for($i = 0; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notlar</label>
                        <textarea class="form-control" id="edit_notes" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_notify_on_sale" checked>
                            <label class="custom-control-label" for="edit_notify_on_sale">
                                İndirimde bildirim al
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="saveWishlistEdit()">Kaydet</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle wishlist (for product pages)
function toggleWishlist(productId) {
    $.ajax({
        url: '{{ route("wishlist.toggle") }}',
        method: 'POST',
        data: {
            product_id: productId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showNotification(response.message, response.action === 'added' ? 'success' : 'info');
                updateWishlistCount(response.summary.total_items);
            }
        }
    });
}

// Remove from wishlist
function removeFromWishlist(wishlistId) {
    if (confirm('Bu ürünü favorilerinizden çıkarmak istediğinize emin misiniz?')) {
        $.ajax({
            url: '/wishlist/' + wishlistId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#wishlist-item-' + wishlistId).fadeOut();
                    showNotification(response.message, 'success');
                    updateSummary(response.summary);
                }
            }
        });
    }
}

// Update priority
function updatePriority(wishlistId, priority) {
    $.ajax({
        url: '/wishlist/' + wishlistId,
        method: 'PUT',
        data: {
            priority: priority,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showNotification('Öncelik güncellendi', 'success');
            }
        }
    });
}

// Edit wishlist item
function editWishlistItem(wishlistId) {
    // Fetch current data and populate modal
    $('#edit_wishlist_id').val(wishlistId);
    $('#editWishlistModal').modal('show');
}

// Save wishlist edit
function saveWishlistEdit() {
    const wishlistId = $('#edit_wishlist_id').val();
    $.ajax({
        url: '/wishlist/' + wishlistId,
        method: 'PUT',
        data: {
            priority: $('#edit_priority').val(),
            notes: $('#edit_notes').val(),
            notify_on_sale: $('#edit_notify_on_sale').is(':checked'),
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#editWishlistModal').modal('hide');
                showNotification(response.message, 'success');
                location.reload();
            }
        }
    });
}

// Clear all wishlist
function clearWishlist() {
    if (confirm('Tüm favorilerinizi temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
        $.ajax({
            url: '{{ route("wishlist.clear") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                }
            }
        });
    }
}

// Add all to cart
function addAllToCart() {
    $.ajax({
        url: '{{ route("wishlist.add-all-to-cart") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
            }
        }
    });
}

// Share wishlist
function shareWishlist() {
    $.ajax({
        url: '{{ route("wishlist.share") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const shareUrl = response.share_url;
                // Copy to clipboard
                navigator.clipboard.writeText(shareUrl).then(function() {
                    showNotification('Paylaşım linki kopyalandı!', 'success');
                });
            }
        }
    });
}

// Add to cart (placeholder)
function addToCart(productId) {
    // This will be implemented when Cart module is created
    showNotification('Sepet modülü henüz hazır değil', 'info');
}

// Update summary
function updateSummary(summary) {
    // Update summary cards with new data
    location.reload(); // For now, just reload
}

// Update wishlist count in header
function updateWishlistCount(count) {
    $('.wishlist-count').text(count);
}

// Show notification
function showNotification(message, type) {
    // You can use any notification library here
    alert(message);
}
</script>
@endpush

@push('styles')
<style>
.wishlist-card {
    transition: transform 0.2s;
}
.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush
