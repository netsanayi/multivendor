<?php

namespace App\Modules\Wishlists\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Modules\Products\Models\Product;

class Wishlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'priority',
        'notes',
        'added_at',
        'notified_at',
        'price_when_added',
        'notify_on_sale',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
        'added_at' => 'datetime',
        'notified_at' => 'datetime',
        'price_when_added' => 'decimal:2',
        'notify_on_sale' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wishlist) {
            if (empty($wishlist->added_at)) {
                $wishlist->added_at = now();
            }
            
            // Ürünün mevcut fiyatını kaydet
            if (empty($wishlist->price_when_added)) {
                $product = Product::find($wishlist->product_id);
                if ($product) {
                    $wishlist->price_when_added = $product->price;
                }
            }
        });
    }

    /**
     * Get the user that owns the wishlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('added_at', 'desc');
    }

    /**
     * Scope a query to only include items that should be notified.
     */
    public function scopeNotifiable($query)
    {
        return $query->where('notify_on_sale', true)
            ->whereNull('notified_at');
    }

    /**
     * Check if the product is on sale compared to when it was added.
     */
    public function isOnSale(): bool
    {
        if (!$this->product || !$this->price_when_added) {
            return false;
        }

        return $this->product->price < $this->price_when_added;
    }

    /**
     * Get the discount percentage if product is on sale.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->isOnSale()) {
            return null;
        }

        $discount = (($this->price_when_added - $this->product->price) / $this->price_when_added) * 100;
        return round($discount, 2);
    }

    /**
     * Get the discount amount if product is on sale.
     */
    public function getDiscountAmountAttribute(): ?float
    {
        if (!$this->isOnSale()) {
            return null;
        }

        return round($this->price_when_added - $this->product->price, 2);
    }

    /**
     * Mark as notified.
     */
    public function markAsNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }

    /**
     * Reset notification.
     */
    public function resetNotification(): void
    {
        $this->update([
            'notified_at' => null,
            'price_when_added' => $this->product->price,
        ]);
    }

    /**
     * Update priority.
     */
    public function updatePriority(int $priority): void
    {
        $this->update(['priority' => $priority]);
    }

    /**
     * Check if user has the product in wishlist.
     */
    public static function userHasProduct(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Add product to user's wishlist.
     */
    public static function addProduct(int $userId, int $productId, array $data = []): ?self
    {
        // Check if already exists
        if (static::userHasProduct($userId, $productId)) {
            return null;
        }

        return static::create(array_merge([
            'user_id' => $userId,
            'product_id' => $productId,
        ], $data));
    }

    /**
     * Remove product from user's wishlist.
     */
    public static function removeProduct(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Toggle product in user's wishlist.
     */
    public static function toggleProduct(int $userId, int $productId, array $data = []): array
    {
        if (static::userHasProduct($userId, $productId)) {
            static::removeProduct($userId, $productId);
            return ['action' => 'removed', 'wishlist' => null];
        } else {
            $wishlist = static::addProduct($userId, $productId, $data);
            return ['action' => 'added', 'wishlist' => $wishlist];
        }
    }

    /**
     * Get user's wishlist summary.
     */
    public static function getUserSummary(int $userId): array
    {
        $wishlists = static::with('product')
            ->forUser($userId)
            ->get();

        $totalItems = $wishlists->count();
        $totalValue = $wishlists->sum(function ($item) {
            return $item->product ? $item->product->price : 0;
        });
        $onSaleCount = $wishlists->filter(function ($item) {
            return $item->isOnSale();
        })->count();
        $totalSavings = $wishlists->sum(function ($item) {
            return $item->discount_amount ?? 0;
        });

        return [
            'total_items' => $totalItems,
            'total_value' => round($totalValue, 2),
            'on_sale_count' => $onSaleCount,
            'total_savings' => round($totalSavings, 2),
        ];
    }
}
