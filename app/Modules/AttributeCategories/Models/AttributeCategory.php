<?php

namespace App\Modules\AttributeCategories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the category image.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Uploads\Models\Upload::class, 'image_id');
    }

    /**
     * Get the product attributes for this category.
     */
    public function productAttributes(): HasMany
    {
        return $this->hasMany(\App\Modules\ProductAttributes\Models\ProductAttribute::class, 'attribute_category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get active attributes count.
     */
    public function getActiveAttributesCountAttribute()
    {
        return $this->productAttributes()->active()->count();
    }

    /**
     * Check if the category has attributes.
     */
    public function hasAttributes(): bool
    {
        return $this->productAttributes()->exists();
    }
}
