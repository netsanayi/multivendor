<?php

namespace App\Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'product_code',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'barcode',
        'default_price',
        'default_currency_id',
        'condition',
        'stock_quantity',
        'min_sale_quantity',
        'max_sale_quantity',
        'length',
        'width',
        'height',
        'weight',
        'approval_status',
        'category_id',
        'brand_id',
        'attributes',
        'similar_products',
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
        'tags' => 'array',
        'attributes' => 'array',
        'similar_products' => 'array',
        'discount' => 'array',
        'images' => 'array',
        'default_price' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_sale_quantity' => 'integer',
        'max_sale_quantity' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Categories\Models\Category::class, 'category_id');
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Brands\Models\Brand::class, 'brand_id');
    }

    /**
     * Get the default currency for the product.
     */
    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Currencies\Models\Currency::class, 'default_currency_id');
    }

    /**
     * Get the vendor products for the product.
     */
    public function vendorProducts(): HasMany
    {
        return $this->hasMany(\App\Modules\VendorProducts\Models\VendorProduct::class, 'relation_id');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include approved products.
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope a query to only include products pending approval.
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Check if the product is on discount.
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
            return $this->default_price;
        }

        $discountValue = $this->discount['value'] ?? 0;
        $discountType = $this->discount['type'] ?? 'percentage';

        if ($discountType === 'percentage') {
            return $this->default_price * (1 - $discountValue / 100);
        } else {
            return max(0, $this->default_price - $discountValue);
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
     * Check if the product is approved.
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if the product is pending approval.
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if the product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get the main image URL.
     */
    public function getMainImageAttribute()
    {
        if (empty($this->images)) {
            return null;
        }

        $mainImageId = $this->images[0] ?? null;
        if (!$mainImageId) {
            return null;
        }

        // Burada Upload modeli ile ilişki kurulacak
        return \App\Modules\Uploads\Models\Upload::find($mainImageId);
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

    /**
     * Add a tag to the product.
     */
    public function addTag(string $tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            $this->save();
        }
    }

    /**
     * Remove a tag from the product.
     */
    public function removeTag(string $tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_values(array_diff($tags, [$tag]));
        $this->tags = $tags;
        $this->save();
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
}
