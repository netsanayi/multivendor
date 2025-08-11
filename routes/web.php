<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ana sayfa
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Jetstream Auth Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Panel Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        
        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        // Categories
        Route::resource('categories', \App\Modules\Categories\Controllers\CategoryController::class);
        Route::post('categories/update-order', [\App\Modules\Categories\Controllers\CategoryController::class, 'updateOrder'])
            ->name('categories.update-order');
        
        // Products
        Route::resource('products', \App\Modules\Products\Controllers\ProductController::class);
        Route::post('products/{product}/approve', [\App\Modules\Products\Controllers\ProductController::class, 'approve'])
            ->name('products.approve');
        Route::post('products/{product}/reject', [\App\Modules\Products\Controllers\ProductController::class, 'reject'])
            ->name('products.reject');
        Route::delete('products/{product}/remove-image', [\App\Modules\Products\Controllers\ProductController::class, 'removeImage'])
            ->name('products.remove-image');
        
        // Vendor Products
        Route::resource('vendor-products', \App\Modules\VendorProducts\Controllers\VendorProductController::class);
        
        // Product Attributes
        Route::resource('product-attributes', \App\Modules\ProductAttributes\Controllers\ProductAttributeController::class);
        
        // Attribute Categories
        Route::resource('attribute-categories', \App\Modules\AttributeCategories\Controllers\AttributeCategoryController::class);
        
        // Brands
        Route::resource('brands', \App\Modules\Brands\Controllers\BrandController::class);
        Route::post('brands/update-order', [\App\Modules\Brands\Controllers\BrandController::class, 'updateOrder'])
            ->name('brands.update-order');
        
        // Blogs
        Route::resource('blogs', \App\Modules\Blogs\Controllers\BlogController::class);
        
        // Users
        Route::resource('users', \App\Modules\Users\Controllers\UserController::class);
        Route::post('users/{user}/toggle-status', [\App\Modules\Users\Controllers\UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        
        // Roles
        Route::resource('roles', \App\Modules\Roles\Controllers\RoleController::class);
        
        // Addresses
        Route::resource('addresses', \App\Modules\Addresses\Controllers\AddressController::class);
        
        // Currencies
        Route::resource('currencies', \App\Modules\Currencies\Controllers\CurrencyController::class);
        Route::post('currencies/update-rates', [\App\Modules\Currencies\Controllers\CurrencyController::class, 'updateRates'])
            ->name('currencies.update-rates');
        
        // Languages
        Route::resource('languages', \App\Modules\Languages\Controllers\LanguageController::class);
        Route::post('languages/update-order', [\App\Modules\Languages\Controllers\LanguageController::class, 'updateOrder'])
            ->name('languages.update-order');
        
        // Banners
        Route::resource('banners', \App\Modules\Banners\Controllers\BannerController::class);
        
        // Activity Log
        Route::prefix('activity-log')->name('activity-log.')->group(function () {
            Route::get('/', [\App\Modules\ActivityLog\Controllers\ActivityLogController::class, 'index'])->name('index');
            Route::get('/export', [\App\Modules\ActivityLog\Controllers\ActivityLogController::class, 'export'])->name('export');
            Route::post('/clear', [\App\Modules\ActivityLog\Controllers\ActivityLogController::class, 'clear'])->name('clear');
            Route::get('/{activity}', [\App\Modules\ActivityLog\Controllers\ActivityLogController::class, 'show'])->name('show');
        });
        
        // Notifications (Admin)
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Modules\Notifications\Controllers\NotificationController::class, 'index'])->name('index');
            Route::get('/settings', [\App\Modules\Notifications\Controllers\NotificationController::class, 'settings'])->name('settings');
            Route::post('/settings', [\App\Modules\Notifications\Controllers\NotificationController::class, 'updateSettings'])->name('settings.update');
        });
        
        // Settings
        Route::get('settings', [\App\Modules\Settings\Controllers\SettingController::class, 'index'])
            ->name('settings.index');
        Route::post('settings', [\App\Modules\Settings\Controllers\SettingController::class, 'update'])
            ->name('settings.update');
        Route::post('settings/clear-cache', [\App\Modules\Settings\Controllers\SettingController::class, 'clearCache'])
            ->name('settings.clear-cache');
        
        // Image Upload Route
        Route::post('upload/image', function(\Illuminate\Http\Request $request) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/blog'), $filename);
                return response()->json(['location' => '/uploads/blog/' . $filename]);
            }
            return response()->json(['error' => 'No file uploaded'], 400);
        })->name('upload.image');
        
        // Uploads (AJAX)
        Route::post('uploads', [\App\Modules\Uploads\Controllers\UploadController::class, 'store'])
            ->name('uploads.store');
        Route::delete('uploads/{upload}', [\App\Modules\Uploads\Controllers\UploadController::class, 'destroy'])
            ->name('uploads.destroy');	
			
        // Admin routes içine eklenecek
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        
        // Vendor Management
        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/list', [\App\Http\Controllers\Admin\VendorManagementController::class, 'index'])->name('list');
            Route::get('/applications', [\App\Http\Controllers\Admin\VendorManagementController::class, 'applications'])->name('applications');
            Route::get('/{vendor}', [\App\Http\Controllers\Admin\VendorManagementController::class, 'show'])->name('show');
            Route::post('/{vendor}/approve', [\App\Http\Controllers\Admin\VendorManagementController::class, 'approve'])->name('approve');
            Route::post('/{vendor}/reject', [\App\Http\Controllers\Admin\VendorManagementController::class, 'reject'])->name('reject');
            Route::post('/{vendor}/suspend', [\App\Http\Controllers\Admin\VendorManagementController::class, 'suspend'])->name('suspend');
            Route::post('/{vendor}/activate', [\App\Http\Controllers\Admin\VendorManagementController::class, 'activate'])->name('activate');
            Route::post('/{vendor}/commission', [\App\Http\Controllers\Admin\VendorManagementController::class, 'updateCommission'])->name('update-commission');
        });
        
        Route::get('commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');			
    });

// Vendor Panel Routes
Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        
        // Dashboard
        Route::get('/', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Earnings
        Route::get('/earnings', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'earnings'])
            ->name('earnings');
        
        // Payouts
        Route::get('/payouts', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'payouts'])
            ->name('payouts');
        Route::post('/payouts/request', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'requestPayout'])
            ->name('payouts.request');
        
        // Products
        Route::get('/products', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'products'])
            ->name('products.index');
        Route::resource('products', \App\Modules\VendorProducts\Controllers\VendorProductController::class)
            ->except(['index']);
        
        // Orders
        Route::get('/orders', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'orders'])
            ->name('orders');
        
        // Analytics
        Route::get('/analytics', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'analytics'])
            ->name('analytics');
        
        // Settings
        Route::get('/settings', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'settings'])
            ->name('settings');
        Route::post('/settings', [\App\Modules\VendorDashboard\Controllers\VendorDashboardController::class, 'updateSettings'])
            ->name('settings.update');
    });

// Wishlist Routes
Route::middleware(['auth'])
    ->group(function () {
        Route::get('/wishlist', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'index'])
            ->name('wishlist.index');
        Route::post('/wishlist', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'store'])
            ->name('wishlist.store');
        Route::post('/wishlist/toggle', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'toggle'])
            ->name('wishlist.toggle');
        Route::put('/wishlist/{wishlist}', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'update'])
            ->name('wishlist.update');
        Route::delete('/wishlist/{wishlist}', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'destroy'])
            ->name('wishlist.destroy');
        Route::post('/wishlist/clear', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'clear'])
            ->name('wishlist.clear');
        Route::post('/wishlist/add-all-to-cart', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'addAllToCart'])
            ->name('wishlist.add-all-to-cart');
        Route::get('/wishlist/share', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'share'])
            ->name('wishlist.share');
    });

// Public Wishlist Route (for shared wishlists)
Route::get('/wishlist/shared/{token}', [\App\Modules\Wishlists\Controllers\WishlistController::class, 'shared'])
    ->name('wishlist.shared');

// Frontend Routes
Route::name('frontend.')
    ->group(function () {
        
        // Categories
        // Route::get('category/{category:slug}', [\App\Modules\Categories\Controllers\Frontend\CategoryController::class, 'show'])
        //     ->name('categories.show');
        
        // Products
        // Route::get('product/{product:slug}', [\App\Modules\Products\Controllers\Frontend\ProductController::class, 'show'])
        //     ->name('products.show');
        
        // Brands
        // Route::get('brand/{brand:slug}', [\App\Modules\Brands\Controllers\Frontend\BrandController::class, 'show'])
        //     ->name('brands.show');
        
        // Blogs
        // Route::get('blog', [\App\Modules\Blogs\Controllers\Frontend\BlogController::class, 'index'])
        //     ->name('blogs.index');
        // Route::get('blog/{blog:slug}', [\App\Modules\Blogs\Controllers\Frontend\BlogController::class, 'show'])
        //     ->name('blogs.show');
        
        // Search
        // Route::get('search', [\App\Modules\Products\Controllers\Frontend\SearchController::class, 'index'])
        //     ->name('search');
        
        // Cart (gelecekte eklenecek)
        // Route::get('cart', [\App\Modules\Cart\Controllers\CartController::class, 'index'])
        //     ->name('cart.index');
    });

// Language Switcher
Route::get('language/{code}', function ($code) {
    if (in_array($code, config('app.available_locales', ['tr', 'en']))) {
        session(['locale' => $code]);
    }
    return redirect()->back();
})->name('language.switch');

// Currency Switcher
Route::get('currency/{code}', function ($code) {
    $currency = \App\Modules\Currencies\Models\Currency::where('code', $code)
        ->where('status', true)
        ->first();
    
    if ($currency) {
        session(['currency' => $code]);
    }
    
    return redirect()->back();
})->name('currency.switch');

// Ticket/Support System Routes
Route::middleware(['auth'])
    ->prefix('tickets')
    ->name('tickets.')
    ->group(function () {
        Route::get('/', [\App\Modules\Tickets\Controllers\TicketController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Tickets\Controllers\TicketController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Tickets\Controllers\TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [\App\Modules\Tickets\Controllers\TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/respond', [\App\Modules\Tickets\Controllers\TicketController::class, 'respond'])->name('respond');
        Route::post('/{ticket}/close', [\App\Modules\Tickets\Controllers\TicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/reopen', [\App\Modules\Tickets\Controllers\TicketController::class, 'reopen'])->name('reopen');
        Route::post('/{ticket}/rate', [\App\Modules\Tickets\Controllers\TicketController::class, 'rate'])->name('rate');
    });

// Messaging System Routes
Route::middleware(['auth'])
    ->prefix('messages')
    ->name('messages.')
    ->group(function () {
        Route::get('/', [\App\Modules\Messages\Controllers\MessageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Messages\Controllers\MessageController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Messages\Controllers\MessageController::class, 'store'])->name('store');
        Route::get('/blocked', [\App\Modules\Messages\Controllers\MessageController::class, 'blockedUsers'])->name('blocked');
        Route::get('/{thread}', [\App\Modules\Messages\Controllers\MessageController::class, 'show'])->name('show');
        Route::post('/{thread}/send', [\App\Modules\Messages\Controllers\MessageController::class, 'send'])->name('send');
        Route::post('/{thread}/star', [\App\Modules\Messages\Controllers\MessageController::class, 'toggleStar'])->name('star');
        Route::post('/{thread}/mute', [\App\Modules\Messages\Controllers\MessageController::class, 'toggleMute'])->name('mute');
        Route::post('/{thread}/archive', [\App\Modules\Messages\Controllers\MessageController::class, 'archive'])->name('archive');
        Route::post('/{thread}/leave', [\App\Modules\Messages\Controllers\MessageController::class, 'leave'])->name('leave');
        Route::post('/{thread}/offer/{message}/accept', [\App\Modules\Messages\Controllers\MessageController::class, 'acceptOffer'])->name('offer.accept');
        Route::post('/{thread}/offer/{message}/reject', [\App\Modules\Messages\Controllers\MessageController::class, 'rejectOffer'])->name('offer.reject');
        Route::post('/block', [\App\Modules\Messages\Controllers\MessageController::class, 'blockUser'])->name('block');
        Route::post('/unblock', [\App\Modules\Messages\Controllers\MessageController::class, 'unblockUser'])->name('unblock');
    });

// Notification System Routes
Route::middleware(['auth'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [\App\Modules\Notifications\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/settings', [\App\Modules\Notifications\Controllers\NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Modules\Notifications\Controllers\NotificationController::class, 'updateSettings'])->name('settings.update');
        Route::post('/{id}/read', [\App\Modules\Notifications\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Modules\Notifications\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [\App\Modules\Notifications\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/clear', [\App\Modules\Notifications\Controllers\NotificationController::class, 'clear'])->name('clear');
        Route::post('/push/register', [\App\Modules\Notifications\Controllers\NotificationController::class, 'registerPushToken'])->name('push.register');
        Route::post('/push/unregister', [\App\Modules\Notifications\Controllers\NotificationController::class, 'unregisterPushToken'])->name('push.unregister');
        Route::post('/test', [\App\Modules\Notifications\Controllers\NotificationController::class, 'test'])->name('test');
        
        // AJAX endpoints
        Route::get('/unread-count', [\App\Modules\Notifications\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [\App\Modules\Notifications\Controllers\NotificationController::class, 'recent'])->name('recent');
    });
