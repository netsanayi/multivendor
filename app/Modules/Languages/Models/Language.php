<?php

namespace App\Modules\Languages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'locale',
        'image_id',
        'order',
        'is_rtl',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'is_rtl' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get the flag image.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Uploads\Models\Upload::class, 'image_id');
    }

    /**
     * Scope a query to only include active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Check if this is the current language.
     */
    public function isCurrent(): bool
    {
        return $this->code === app()->getLocale();
    }

    /**
     * Check if this is the default language.
     */
    public function isDefault(): bool
    {
        return $this->code === config('app.locale');
    }

    /**
     * Set as current language.
     */
    public function setAsCurrent()
    {
        app()->setLocale($this->code);
        session(['locale' => $this->code]);
    }

    /**
     * Get the direction attribute.
     */
    public function getDirectionAttribute()
    {
        return $this->is_rtl ? 'rtl' : 'ltr';
    }

    /**
     * Get the HTML lang attribute.
     */
    public function getHtmlLangAttribute()
    {
        return str_replace('_', '-', $this->code);
    }
}
