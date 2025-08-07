<?php

namespace App\Modules\VendorProducts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'relation_id',
        'user_relation_id',
        'price',
        'currency_id',
        'condition',
        'stock_quantity',
        'min_sale_quantity',
        'max_sale_quantity',
        'discount',
        'images',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_sale_quantity' => 'integer',
        'max_sale_quantity' => 'integer',
        'discount' => 'array',
        'images' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Get the product that this vendor product belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Products\Models\Product::class, 'relation_id');
    }

    /**
     * Get the vendor (user) that owns this product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'user_relation_id');
    }

    /**
     * Get the currency for this vendor product.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Currencies\Models\Currency::class, 'currency_id');
    }

    /**
     * Scope a query to only include active vendor products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include vendor products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Check if the vendor product is on discount.
     */
    public function isOnDiscount(): bool
    {
        if (!$this->discount) {
            return false;
        }

        $now = now();
        $startDate = isset($this->discount['start_date']) ? 
            \Carbon\Carbon::parse($this->discount['start_date']) : null;
        $endDate = isset($this->discount['end_date']) ? 
            \Carbon\Carbon::parse($this->discount['end_date']) : null;

        if ($startDate && $now->lt($startDate)) {
            return false;
        }

        if ($endDate && $now->gt($endDate)) {
            return false;
        }

        return isset($this->discount['value']) && $this->discount['value'] > 0;
    }

    /**
     * Get the discounted price.
     */
    public function getDiscountedPriceAttribute()
    {
        if (!$this->isOnDiscount()) {
            return $this->price;
        }

        $discountValue = $this->discount['value'] ?? 0;
        $discountType = $this->discount['type'] ?? 'percentage';

        if ($discountType === 'percentage') {
            return $this->price * (1 - $discountValue / 100);
        } else {
            return max(0, $this->price - $discountValue);
        }
    }

    /**
     * Get the final price (with discount if applicable).
     */
    public function getFinalPriceAttribute()
    {
        return $this->discounted_price;
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(int $quantity, string $operation = 'set')
    {
        switch ($operation) {
            case 'increment':
                $this->stock_quantity += $quantity;
                break;
            case 'decrement':
                $this->stock_quantity = max(0, $this->stock_quantity - $quantity);
                break;
            default:
                $this->stock_quantity = max(0, $quantity);
        }

        $this->save();
    }

    /**
     * Check if the vendor product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get the vendor product display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->product->name . ' - ' . $this->vendor->name;
    }

    /**
     * Get all image models.
     */
    public function getImageModelsAttribute()
    {
        if (empty($this->images)) {
            return collect();
        }

        return \App\Modules\Uploads\Models\Upload::whereIn('id', $this->images)->get();
    }
}
