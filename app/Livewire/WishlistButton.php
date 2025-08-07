<?php

namespace App\Livewire;

use Livewire\Component;
use App\Modules\Wishlists\Models\Wishlist;
use App\Modules\Products\Models\Product;

class WishlistButton extends Component
{
    public $productId;
    public $isInWishlist = false;
    public $showText = true;
    public $size = 'md'; // sm, md, lg
    
    public function mount($productId, $showText = true, $size = 'md')
    {
        $this->productId = $productId;
        $this->showText = $showText;
        $this->size = $size;
        $this->checkWishlistStatus();
    }
    
    public function checkWishlistStatus()
    {
        if (auth()->check()) {
            $this->isInWishlist = Wishlist::userHasProduct(auth()->id(), $this->productId);
        }
    }
    
    public function toggleWishlist()
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }
        
        $result = Wishlist::toggleProduct(auth()->id(), $this->productId);
        
        $this->isInWishlist = $result['action'] === 'added';
        
        // Dispatch browser event for notification
        $this->dispatch('wishlist-updated', [
            'action' => $result['action'],
            'message' => $result['action'] === 'added' 
                ? 'Ürün favorilere eklendi!' 
                : 'Ürün favorilerden çıkarıldı!'
        ]);
        
        // Update wishlist count in header
        $this->dispatch('update-wishlist-count', [
            'count' => Wishlist::where('user_id', auth()->id())->count()
        ]);
    }
    
    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
