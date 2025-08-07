<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL older versions
        Schema::defaultStringLength(191);
        
        // Set default locale for Carbon
        \Carbon\Carbon::setLocale(config('app.locale'));
        
        // Register model observers if needed
        $this->registerObservers();
        
        // Register view composers
        $this->registerViewComposers();
    }
    
    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        // Example:
        // \App\Modules\Products\Models\Product::observe(\App\Observers\ProductObserver::class);
    }
    
    /**
     * Register view composers.
     */
    protected function registerViewComposers(): void
    {
        // Share common data with all views
        view()->composer('*', function ($view) {
            $view->with('currentLocale', app()->getLocale());
            $view->with('availableLocales', config('app.available_locales', ['tr', 'en']));
            
            // Share current currency
            if (session()->has('currency')) {
                $currency = \App\Modules\Currencies\Models\Currency::where('code', session('currency'))
                    ->where('status', true)
                    ->first();
                $view->with('currentCurrency', $currency);
            }
        });
    }
}
