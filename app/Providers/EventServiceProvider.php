<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Product Events
        'App\Events\ProductCreated' => [
            'App\Listeners\NotifyAdminOfNewProduct',
        ],
        
        'App\Events\ProductApproved' => [
            'App\Listeners\NotifyVendorOfApproval',
        ],
        
        // Order Events (for future implementation)
        'App\Events\OrderPlaced' => [
            'App\Listeners\SendOrderConfirmation',
            'App\Listeners\NotifyVendorOfOrder',
            'App\Listeners\UpdateProductStock',
        ],
        
        // Vendor Events
        'App\Events\VendorRegistered' => [
            'App\Listeners\SendVendorWelcomeEmail',
            'App\Listeners\NotifyAdminOfNewVendor',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
