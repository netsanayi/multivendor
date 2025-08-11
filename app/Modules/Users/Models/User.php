<?php

namespace App\Modules\Users\Models;

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
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
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
     * Get the notification settings for the user.
     */
    public function notificationSettings()
    {
        return $this->hasOne(\App\Modules\Notifications\Models\NotificationSettings::class, 'user_id');
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
        return $query->role('vendor');
    }

    /**
     * Scope a query to only include customers.
     */
    public function scopeCustomers($query)
    {
        return $query->role('customer');
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
        return $this->hasRole('admin');
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
}
