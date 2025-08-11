<?php $__env->startSection('title', 'Favorilerim'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Favorilerim</h1>
            <p class="text-muted">
                <i class="fas fa-heart text-danger"></i> 
                <?php echo e($wishlists->total()); ?> ürün listeleniyor
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
                    <h5 class="card-title"><?php echo e($summary['total_items']); ?></h5>
                    <p class="card-text text-muted">Toplam Ürün</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">₺<?php echo e(number_format($summary['total_value'], 2)); ?></h5>
                    <p class="card-text text-muted">Toplam Değer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success"><?php echo e($summary['on_sale_count']); ?></h5>
                    <p class="card-text text-muted">İndirimde</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">₺<?php echo e(number_format($summary['total_savings'], 2)); ?></h5>
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
                        <a href="<?php echo e(route('wishlist.index', ['sort' => 'priority'])); ?>" 
                           class="btn btn-sm <?php echo e($sortBy == 'priority' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            Öncelik
                        </a>
                        <a href="<?php echo e(route('wishlist.index', ['sort' => 'date'])); ?>" 
                           class="btn btn-sm <?php echo e($sortBy == 'date' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            Tarih
                        </a>
                        <a href="<?php echo e(route('wishlist.index', ['sort' => 'name'])); ?>" 
                           class="btn btn-sm <?php echo e($sortBy == 'name' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            İsim
                        </a>
                        <a href="<?php echo e(route('wishlist.index', ['sort' => 'price_low'])); ?>" 
                           class="btn btn-sm <?php echo e($sortBy == 'price_low' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            Fiyat ↑
                        </a>
                        <a href="<?php echo e(route('wishlist.index', ['sort' => 'price_high'])); ?>" 
                           class="btn btn-sm <?php echo e($sortBy == 'price_high' ? 'btn-primary' : 'btn-outline-primary'); ?>">
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
        <?php $__empty_1 = true; $__currentLoopData = $wishlists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wishlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4 col-lg-3 mb-4" id="wishlist-item-<?php echo e($wishlist->id); ?>">
            <div class="card h-100 wishlist-card">
                <!-- Sale Badge -->
                <?php if($wishlist->isOnSale()): ?>
                <div class="badge badge-danger position-absolute" style="top: 10px; right: 10px; z-index: 1;">
                    -<?php echo e($wishlist->discount_percentage); ?>%
                </div>
                <?php endif; ?>

                <!-- Remove Button -->
                <button class="btn btn-sm btn-light position-absolute" 
                        style="top: 10px; left: 10px; z-index: 1;"
                        onclick="removeFromWishlist(<?php echo e($wishlist->id); ?>)">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Product Image -->
                <?php if($wishlist->product->images && $wishlist->product->images->count() > 0): ?>
                    <img src="<?php echo e($wishlist->product->images->first()->url); ?>" 
                         class="card-img-top" 
                         alt="<?php echo e($wishlist->product->name); ?>"
                         style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <!-- Priority Stars -->
                    <div class="mb-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo e($i <= $wishlist->priority ? 'text-warning' : 'text-muted'); ?>" 
                               style="cursor: pointer; font-size: 12px;"
                               onclick="updatePriority(<?php echo e($wishlist->id); ?>, <?php echo e($i); ?>)"></i>
                        <?php endfor; ?>
                    </div>

                    <!-- Product Name -->
                    <h6 class="card-title">
                        <a href="<?php echo e(route('frontend.products.show', $wishlist->product->slug)); ?>" 
                           class="text-dark text-decoration-none">
                            <?php echo e(Str::limit($wishlist->product->name, 50)); ?>

                        </a>
                    </h6>

                    <!-- Category -->
                    <?php if($wishlist->product->category): ?>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-folder"></i> <?php echo e($wishlist->product->category->name); ?>

                    </p>
                    <?php endif; ?>

                    <!-- Price -->
                    <div class="mb-2">
                        <?php if($wishlist->isOnSale()): ?>
                            <span class="text-muted text-decoration-line-through">
                                ₺<?php echo e(number_format($wishlist->price_when_added, 2)); ?>

                            </span>
                            <span class="h5 text-danger ml-2">
                                ₺<?php echo e(number_format($wishlist->product->price, 2)); ?>

                            </span>
                        <?php else: ?>
                            <span class="h5">
                                ₺<?php echo e(number_format($wishlist->product->price, 2)); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Notes -->
                    <?php if($wishlist->notes): ?>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-sticky-note"></i> <?php echo e(Str::limit($wishlist->notes, 50)); ?>

                    </p>
                    <?php endif; ?>

                    <!-- Added Date -->
                    <p class="text-muted small mb-3">
                        <i class="fas fa-calendar"></i> <?php echo e($wishlist->added_at->diffForHumans()); ?>

                    </p>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo e($wishlist->product->id); ?>)">
                            <i class="fas fa-shopping-cart"></i> Sepete Ekle
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" 
                                onclick="editWishlistItem(<?php echo e($wishlist->id); ?>)">
                            <i class="fas fa-edit"></i> Düzenle
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-heart fa-5x text-muted mb-3"></i>
                <h4>Favori listeniz boş</h4>
                <p class="text-muted">Beğendiğiniz ürünleri favorilerinize ekleyerek daha sonra kolayca ulaşabilirsiniz.</p>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($wishlists->hasPages()): ?>
    <div class="row">
        <div class="col-12">
            <?php echo e($wishlists->withQueryString()->links()); ?>

        </div>
    </div>
    <?php endif; ?>
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
                            <?php for($i = 0; $i <= 10; $i++): ?>
                            <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                            <?php endfor; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Toggle wishlist (for product pages)
function toggleWishlist(productId) {
    $.ajax({
        url: '<?php echo e(route("wishlist.toggle")); ?>',
        method: 'POST',
        data: {
            product_id: productId,
            _token: '<?php echo e(csrf_token()); ?>'
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
                _token: '<?php echo e(csrf_token()); ?>'
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
            _token: '<?php echo e(csrf_token()); ?>'
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
            _token: '<?php echo e(csrf_token()); ?>'
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
            url: '<?php echo e(route("wishlist.clear")); ?>',
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
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
        url: '<?php echo e(route("wishlist.add-all-to-cart")); ?>',
        method: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>'
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
        url: '<?php echo e(route("wishlist.share")); ?>',
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.wishlist-card {
    transition: transform 0.2s;
}
.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/wishlists/index.blade.php ENDPATH**/ ?>