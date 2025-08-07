<div>
    <button 
        wire:click="toggleWishlist"
        wire:loading.attr="disabled"
        class="btn btn-{{ $size === 'sm' ? 'sm' : ($size === 'lg' ? 'lg' : '') }} {{ $isInWishlist ? 'btn-danger' : 'btn-outline-danger' }} wishlist-btn"
        title="{{ $isInWishlist ? 'Favorilerden Çıkar' : 'Favorilere Ekle' }}">
        
        <i class="fas fa-heart {{ !$isInWishlist ? 'far' : '' }}" wire:loading.remove></i>
        <i class="fas fa-spinner fa-spin" wire:loading></i>
        
        @if($showText)
            <span wire:loading.remove>
                {{ $isInWishlist ? 'Favorilerde' : 'Favorilere Ekle' }}
            </span>
        @endif
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('wishlist-updated', (data) => {
            // Show notification (you can use any notification library)
            if (typeof showNotification === 'function') {
                showNotification(data[0].message, data[0].action === 'added' ? 'success' : 'info');
            } else {
                console.log(data[0].message);
            }
        });
        
        Livewire.on('show-login-modal', () => {
            // Show login modal or redirect to login
            if (typeof showLoginModal === 'function') {
                showLoginModal();
            } else {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
            }
        });
        
        Livewire.on('update-wishlist-count', (data) => {
            // Update wishlist count in header
            document.querySelectorAll('.wishlist-count').forEach(el => {
                el.textContent = data[0].count;
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
.wishlist-btn {
    transition: all 0.3s ease;
}
.wishlist-btn:hover {
    transform: scale(1.05);
}
.wishlist-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush
