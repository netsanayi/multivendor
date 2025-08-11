<?php

namespace App\Modules\Currencies\Services;

use App\Modules\Currencies\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Cache key for currencies
     */
    const CACHE_KEY = 'currencies_list';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all currencies with caching
     *
     * @param array $filters
     * @param int|null $perPage
     * @return mixed
     */
    public function getAllCurrencies(array $filters = [], ?int $perPage = null)
    {
        $query = Currency::query();

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_default'])) {
            $query->where('is_default', $filters['is_default']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'display_order';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Return paginated or all results
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get active currencies
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCurrencies()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Currency::where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get default currency
     *
     * @return Currency|null
     */
    public function getDefaultCurrency()
    {
        return Cache::remember('default_currency', self::CACHE_TTL, function () {
            return Currency::where('is_default', true)->first();
        });
    }

    /**
     * Get currency by code
     *
     * @param string $code
     * @return Currency|null
     */
    public function getCurrencyByCode(string $code)
    {
        return Cache::remember("currency_{$code}", self::CACHE_TTL, function () use ($code) {
            return Currency::where('code', strtoupper($code))->first();
        });
    }

    /**
     * Create a new currency
     *
     * @param array $data
     * @return Currency
     */
    public function createCurrency(array $data): Currency
    {
        return DB::transaction(function () use ($data) {
            // If this is set as default, unset other defaults
            if ($data['is_default'] ?? false) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }

            // Create currency
            $currency = Currency::create($data);

            // Clear cache
            $this->clearCache();

            return $currency;
        });
    }

    /**
     * Update a currency
     *
     * @param Currency $currency
     * @param array $data
     * @return Currency
     */
    public function updateCurrency(Currency $currency, array $data): Currency
    {
        return DB::transaction(function () use ($currency, $data) {
            // If this is set as default, unset other defaults
            if (($data['is_default'] ?? false) && !$currency->is_default) {
                Currency::where('is_default', true)
                    ->where('id', '!=', $currency->id)
                    ->update(['is_default' => false]);
            }

            // Prevent removing default status if it's the only active currency
            if ($currency->is_default && !($data['is_default'] ?? true)) {
                $activeCurrenciesCount = Currency::where('is_active', true)->count();
                if ($activeCurrenciesCount <= 1) {
                    throw new \Exception('En az bir aktif varsayılan para birimi olmalıdır.');
                }
            }

            // Update currency
            $currency->update($data);

            // Clear cache
            $this->clearCache();

            return $currency->fresh();
        });
    }

    /**
     * Delete a currency
     *
     * @param Currency $currency
     * @return bool
     * @throws \Exception
     */
    public function deleteCurrency(Currency $currency): bool
    {
        // Prevent deleting default currency
        if ($currency->is_default) {
            throw new \Exception('Varsayılan para birimi silinemez.');
        }

        // Check if currency is used in products, orders, etc.
        if ($this->isCurrencyInUse($currency)) {
            throw new \Exception('Bu para birimi kullanımda olduğu için silinemez.');
        }

        $deleted = $currency->delete();

        if ($deleted) {
            $this->clearCache();
        }

        return $deleted;
    }

    /**
     * Toggle currency active status
     *
     * @param Currency $currency
     * @return Currency
     * @throws \Exception
     */
    public function toggleActive(Currency $currency): Currency
    {
        // Prevent deactivating default currency
        if ($currency->is_default && $currency->is_active) {
            throw new \Exception('Varsayılan para birimi deaktif edilemez.');
        }

        $currency->update(['is_active' => !$currency->is_active]);
        $this->clearCache();

        return $currency;
    }

    /**
     * Set currency as default
     *
     * @param Currency $currency
     * @return Currency
     * @throws \Exception
     */
    public function setAsDefault(Currency $currency): Currency
    {
        if (!$currency->is_active) {
            throw new \Exception('Aktif olmayan bir para birimi varsayılan olarak ayarlanamaz.');
        }

        return DB::transaction(function () use ($currency) {
            // Unset other defaults
            Currency::where('is_default', true)
                ->where('id', '!=', $currency->id)
                ->update(['is_default' => false]);

            // Set this as default
            $currency->update(['is_default' => true]);

            // Clear cache
            $this->clearCache();

            return $currency;
        });
    }

    /**
     * Update exchange rates from external API
     *
     * @param string|null $baseCurrency
     * @return array
     */
    public function updateExchangeRates(?string $baseCurrency = null): array
    {
        $baseCurrency = $baseCurrency ?: $this->getDefaultCurrency()->code;
        $updated = [];
        $failed = [];

        try {
            // Use a free API like exchangerate-api.com or fixer.io
            // This is an example using exchangerate-api.com
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$baseCurrency}");

            if ($response->successful()) {
                $rates = $response->json()['rates'] ?? [];

                foreach (Currency::where('code', '!=', $baseCurrency)->get() as $currency) {
                    if (isset($rates[$currency->code])) {
                        $currency->update(['exchange_rate' => $rates[$currency->code]]);
                        $updated[] = $currency->code;
                    } else {
                        $failed[] = $currency->code;
                    }
                }

                // Clear cache
                $this->clearCache();
            } else {
                Log::error('Failed to fetch exchange rates', ['response' => $response->body()]);
                throw new \Exception('Döviz kurları güncellenemedi.');
            }
        } catch (\Exception $e) {
            Log::error('Error updating exchange rates', ['error' => $e->getMessage()]);
            throw $e;
        }

        return [
            'updated' => $updated,
            'failed' => $failed,
            'base_currency' => $baseCurrency
        ];
    }

    /**
     * Convert amount between currencies
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public function convertAmount(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $from = $this->getCurrencyByCode($fromCurrency);
        $to = $this->getCurrencyByCode($toCurrency);

        if (!$from || !$to) {
            throw new \Exception('Geçersiz para birimi kodu.');
        }

        // Convert to base currency first, then to target currency
        $defaultCurrency = $this->getDefaultCurrency();
        
        if ($from->code === $defaultCurrency->code) {
            // From is default, just multiply by target rate
            return round($amount * $to->exchange_rate, $to->decimal_places);
        } elseif ($to->code === $defaultCurrency->code) {
            // To is default, divide by source rate
            return round($amount / $from->exchange_rate, $to->decimal_places);
        } else {
            // Neither is default, convert through default
            $inDefault = $amount / $from->exchange_rate;
            return round($inDefault * $to->exchange_rate, $to->decimal_places);
        }
    }

    /**
     * Format amount with currency
     *
     * @param float $amount
     * @param string|null $currencyCode
     * @return string
     */
    public function formatAmount(float $amount, ?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode) 
            : $this->getDefaultCurrency();

        if (!$currency) {
            return number_format($amount, 2);
        }

        $formatted = number_format(
            $amount,
            $currency->decimal_places,
            $currency->decimal_separator,
            $currency->thousand_separator
        );

        if ($currency->symbol_position === 'before') {
            return $currency->symbol . $formatted;
        } else {
            return $formatted . $currency->symbol;
        }
    }

    /**
     * Check if currency is in use
     *
     * @param Currency $currency
     * @return bool
     */
    private function isCurrencyInUse(Currency $currency): bool
    {
        // Check if used in products
        if (DB::table('products')->where('currency_id', $currency->id)->exists()) {
            return true;
        }

        // Check if used in orders
        if (DB::table('orders')->where('currency_id', $currency->id)->exists()) {
            return true;
        }

        // Check if used in vendor settings
        if (DB::table('vendor_settings')->where('currency_id', $currency->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get currency statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember('currency_statistics', 3600, function () {
            return [
                'total' => Currency::count(),
                'active' => Currency::where('is_active', true)->count(),
                'inactive' => Currency::where('is_active', false)->count(),
                'default' => Currency::where('is_default', true)->value('code'),
                'last_rate_update' => Currency::max('updated_at'),
                'currencies_by_country' => Currency::whereNotNull('country')
                    ->groupBy('country')
                    ->selectRaw('country, count(*) as count')
                    ->pluck('count', 'country')
                    ->toArray()
            ];
        });
    }

    /**
     * Clear currency cache
     *
     * @return void
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('default_currency');
        Cache::forget('currency_statistics');
        
        // Clear individual currency caches
        Currency::all()->each(function ($currency) {
            Cache::forget("currency_{$currency->code}");
        });
    }
}
