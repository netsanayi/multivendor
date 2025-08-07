<?php

namespace App\Modules\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_name',
        'city',
        'district',
        'street',
        'road_name',
        'door_no',
        'building_no',
        'floor',
        'company_type',
        'company_name',
        'tax_office',
        'tax_no',
        'tc_id_no',
        'full_address',
        'is_default',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($address) {
            // Eğer bu kullanıcının ilk adresi ise varsayılan yap
            if (!$address->user->addresses()->exists()) {
                $address->is_default = true;
            }
            
            // Tam adresi otomatik oluştur
            $address->generateFullAddress();
        });

        static::updating(function ($address) {
            // Tam adresi güncelle
            $address->generateFullAddress();
        });

        static::saved(function ($address) {
            // Eğer bu adres varsayılan olarak işaretlendiyse, diğerlerini kaldır
            if ($address->is_default) {
                $address->user->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'user_id');
    }

    /**
     * Scope a query to only include active addresses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include default address.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if the address is corporate.
     */
    public function isCorporate(): bool
    {
        return $this->company_type === 'corporate';
    }

    /**
     * Check if the address is individual.
     */
    public function isIndividual(): bool
    {
        return $this->company_type === 'individual';
    }

    /**
     * Set as default address.
     */
    public function setAsDefault()
    {
        $this->is_default = true;
        $this->save();
    }

    /**
     * Generate full address text.
     */
    public function generateFullAddress()
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->road_name) {
            $parts[] = $this->road_name;
        }

        $buildingInfo = [];
        if ($this->building_no) {
            $buildingInfo[] = "No: " . $this->building_no;
        }
        if ($this->floor) {
            $buildingInfo[] = "Kat: " . $this->floor;
        }
        if ($this->door_no) {
            $buildingInfo[] = "Daire: " . $this->door_no;
        }
        
        if (!empty($buildingInfo)) {
            $parts[] = implode(' ', $buildingInfo);
        }

        $parts[] = $this->district . '/' . $this->city;

        $this->full_address = implode(' ', $parts);
    }

    /**
     * Get formatted address for display.
     */
    public function getFormattedAddressAttribute()
    {
        return $this->full_address;
    }

    /**
     * Get the billing information.
     */
    public function getBillingInfoAttribute()
    {
        if ($this->isCorporate()) {
            return [
                'type' => 'corporate',
                'name' => $this->company_name,
                'tax_office' => $this->tax_office,
                'tax_no' => $this->tax_no,
            ];
        }

        return [
            'type' => 'individual',
            'name' => $this->user->full_name,
            'tc_id_no' => $this->tc_id_no,
        ];
    }
}
