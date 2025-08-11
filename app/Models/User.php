<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'default_currency_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
    ];

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name || $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name;
    }

    /**
     * Get the user's name attribute (for compatibility).
     */
    public function getNameAttribute()
    {
        if (isset($this->attributes['name'])) {
            return $this->attributes['name'];
        }
        return $this->full_name;
    }

    /**
     * Set the user's name attribute.
     */
    public function setNameAttribute($value)
    {
        // Eğer name alanı ayarlanıyorsa, first_name ve last_name'e böl
        if ($value) {
            $parts = explode(' ', $value, 2);
            $this->attributes['first_name'] = $parts[0];
            $this->attributes['last_name'] = $parts[1] ?? '';
            $this->attributes['name'] = $value;
        }
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        $firstName = $this->first_name ?: '';
        $lastName = $this->last_name ?: '';
        
        if (!$firstName && !$lastName && $this->name) {
            $parts = explode(' ', $this->name);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
        }
        
        return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    }

    /**
     * Get the default currency for the user.
     */
    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Currencies\Models\Currency::class, 'default_currency_id');
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Modules\Addresses\Models\Address::class, 'user_id');
    }

    /**
     * Get the vendor products for the user.
     */
    public function vendorProducts(): HasMany
    {
        return $this->hasMany(\App\Modules\VendorProducts\Models\VendorProduct::class, 'user_relation_id');
    }

    /**
     * Get the blogs authored by the user.
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(\App\Modules\Blogs\Models\Blog::class, 'author_id');
    }

    /**
     * Get the wishlists for the user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(\App\Modules\Wishlists\Models\Wishlist::class, 'user_id');
    }

    /**
     * Get the tickets for the user.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(\App\Modules\Tickets\Models\Ticket::class, 'user_id');
    }

    /**
     * Get the message threads for the user.
     */
    public function messageThreads()
    {
        return $this->belongsToMany(
            \App\Modules\Messages\Models\MessageThread::class,
            'message_thread_participants',
            'user_id',
            'thread_id'
        )->withPivot('is_starred', 'is_muted', 'last_read_at', 'left_at')
          ->withTimestamps();
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include vendors.
     */
    public function scopeVendors($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        });
    }

    /**
     * Scope a query to only include customers.
     */
    public function scopeCustomers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'customer');
        });
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Check if the user is a vendor.
     */
    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Get the user's default address.
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    /**
     * Get the user's active vendor products count.
     */
    public function getActiveVendorProductsCountAttribute()
    {
        return $this->vendorProducts()->active()->count();
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus()
    {
        $this->status = !$this->status;
        $this->save();
    }

    /**
     * Vendor earnings relationship
     */
    public function vendorEarnings()
    {
        return $this->hasMany(\App\Modules\VendorDashboard\Models\VendorEarning::class, 'vendor_id');
    }

    /**
     * Vendor payouts relationship
     */
    public function vendorPayouts()
    {
        return $this->hasMany(\App\Modules\VendorDashboard\Models\VendorPayout::class, 'vendor_id');
    }

    /**
     * Get total earnings for vendor
     */
    public function getTotalEarningsAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }
        return $this->vendorEarnings()->sum('amount');
    }

    /**
     * Get pending earnings for vendor
     */
    public function getPendingEarningsAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }
        return $this->vendorEarnings()->where('status', 'pending')->sum('amount');
    }

    /**
     * Get available balance for vendor
     */
    public function getAvailableBalanceAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }
        $totalEarnings = $this->vendorEarnings()->where('status', 'completed')->sum('amount');
        $totalPayouts = $this->vendorPayouts()->whereIn('status', ['completed', 'processing'])->sum('amount');
        return $totalEarnings - $totalPayouts;
    }
}
