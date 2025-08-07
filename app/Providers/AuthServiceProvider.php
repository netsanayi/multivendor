<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Product approval gate
        Gate::define('approve-products', function ($user) {
            return $user->hasPermissionTo('products.approve');
        });

        // Vendor product management
        Gate::define('manage-own-vendor-products', function ($user, $vendorProduct) {
            return $user->id === $vendorProduct->user_relation_id;
        });

        // Define additional gates as needed
    }
}
