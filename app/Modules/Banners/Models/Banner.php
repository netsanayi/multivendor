<?php

namespace App\Modules\Banners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image_id',
        'link',
        'position',
        'order',
        'start_date',
        'end_date',
        'click_count',
        'view_count',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'click_count' => 'integer',
        'view_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * Banner positions.
     */
    const POSITIONS = [
        'home' => 'Ana Sayfa',
        'category' => 'Kategori Sayfası',
        'product' => 'Ürün Sayfası',
        'header' => 'Üst Alan',
        'footer' => 'Alt Alan',
        'sidebar' => 'Yan Menü',
    ];

    /**
     * Get the banner image.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Uploads\Models\Upload::class, 'image_id');
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope a query to only include banners for a specific position.
     */
    public function scopeForPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope a query to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Check if the banner is currently active.
     */
    public function isActive(): bool
    {
        if (!$this->status) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Increment click count.
     */
    public function incrementClickCount()
    {
        $this->increment('click_count');
    }

    /**
     * Get click-through rate (CTR).
     */
    public function getCtrAttribute()
    {
        if ($this->view_count == 0) {
            return 0;
        }

        return round(($this->click_count / $this->view_count) * 100, 2);
    }

    /**
     * Get position name.
     */
    public function getPositionNameAttribute()
    {
        return self::POSITIONS[$this->position] ?? $this->position;
    }

    /**
     * Get remaining days.
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) {
            return null;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }
}
