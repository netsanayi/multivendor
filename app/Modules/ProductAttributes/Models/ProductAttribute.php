<?php

namespace App\Modules\ProductAttributes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image_id',
        'attribute_category_id',
        'product_category_ids',
        'order',
        'values',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_category_ids' => 'array',
        'values' => 'array',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the attribute category.
     */
    public function attributeCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\AttributeCategories\Models\AttributeCategory::class, 'attribute_category_id');
    }

    /**
     * Get the attribute image.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Uploads\Models\Upload::class, 'image_id');
    }

    /**
     * Scope a query to only include active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include attributes for specific category.
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->whereJsonContains('product_category_ids', $categoryId);
    }

    /**
     * Add a value to the attribute.
     */
    public function addValue($value)
    {
        $values = $this->values ?? [];
        if (!in_array($value, $values)) {
            $values[] = $value;
            $this->values = $values;
            $this->save();
        }
    }

    /**
     * Remove a value from the attribute.
     */
    public function removeValue($value)
    {
        $values = $this->values ?? [];
        $values = array_values(array_diff($values, [$value]));
        $this->values = $values;
        $this->save();
    }

    /**
     * Check if the attribute has a specific value.
     */
    public function hasValue($value): bool
    {
        return in_array($value, $this->values ?? []);
    }

    /**
     * Get formatted values for display.
     */
    public function getFormattedValuesAttribute()
    {
        if (empty($this->values)) {
            return '';
        }
        return implode(', ', $this->values);
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
