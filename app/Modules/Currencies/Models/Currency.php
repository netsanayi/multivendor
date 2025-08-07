<?php

namespace App\Modules\Currencies\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'position',
        'exchange_rate',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'status' => 'boolean',
    ];

    /**
     * Scope a query to only include active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Format price with currency.
     */
    public function formatPrice($amount)
    {
        // Simple number formatting with currency symbol
        $formattedAmount = number_format($amount, 2, ',', '.');
        
        if ($this->position === 'left') {
            return $this->symbol . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $this->symbol;
        }
    }

    /**
     * Format price using akaunting/laravel-money when available.
     */
    public function formatPriceWithMoney($amount)
    {
        // This will use akaunting/laravel-money helper if package is installed
        if (function_exists('money')) {
            return money($amount * 100, $this->code)->format();
        }
        
        // Fallback to basic formatting
        return $this->formatPrice($amount);
    }

    /**
     * Convert amount from base currency.
     */
    public function convertFromBase($amount)
    {
        return $amount * $this->exchange_rate;
    }

    /**
     * Convert amount to base currency.
     */
    public function convertToBase($amount)
    {
        if ($this->exchange_rate == 0) {
            return 0;
        }
        
        return $amount / $this->exchange_rate;
    }

    /**
     * Check if this is the base currency.
     */
    public function isBaseCurrency(): bool
    {
        return $this->exchange_rate == 1;
    }

    /**
     * Update exchange rate.
     */
    public function updateExchangeRate($rate)
    {
        $this->exchange_rate = $rate;
        $this->save();
        
        // Log the rate update
        activity()
            ->performedOn($this)
            ->withProperties([
                'old_rate' => $this->getOriginal('exchange_rate'),
                'new_rate' => $rate
            ])
            ->log('Döviz kuru güncellendi');
    }
}
