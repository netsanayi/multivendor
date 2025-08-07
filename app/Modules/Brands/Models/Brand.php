<?php

namespace App\Modules\Brands\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model
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
        'image_id',
        'order',
        'product_category_ids',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_category_ids' => 'array',
        'order' => 'integer',
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
     * Get the brand image.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Uploads\Models\Upload::class, 'image_id');
    }

    /**
     * Get the products for the brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Modules\Products\Models\Product::class, 'brand_id');
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include brands for specific category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->whereJsonContains('product_category_ids', $categoryId);
    }

    /**
     * Get active products count.
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->products()->active()->approved()->count();
    }

    /**
     * Check if the brand has products.
     */
    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /**
     * Add category to product categories.
     */
    public function addProductCategory($categoryId)
    {
        $categories = $this->product_category_ids ?? [];
        if (!in_array($categoryId, $categories)) {
            $categories[] = $categoryId;
            $this->product_category_ids = $categories;
            $this->save();
        }
    }

    /**
     * Remove category from product categories.
     */
    public function removeProductCategory($categoryId)
    {
        $categories = $this->product_category_ids ?? [];
        $categories = array_values(array_diff($categories, [$categoryId]));
        $this->product_category_ids = $categories;
        $this->save();
    }
}
