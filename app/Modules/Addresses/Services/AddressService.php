<?php

namespace App\Modules\Addresses\Services;

use App\Modules\Addresses\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    /**
     * Get all addresses for a user
     */
    public function getUserAddresses($userId)
    {
        return Address::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get default address for a user
     */
    public function getDefaultAddress($userId)
    {
        return Address::where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Get billing address for a user
     */
    public function getBillingAddress($userId)
    {
        return Address::where('user_id', $userId)
            ->where('is_billing', true)
            ->first();
    }

    /**
     * Get shipping address for a user
     */
    public function getShippingAddress($userId)
    {
        return Address::where('user_id', $userId)
            ->where('is_shipping', true)
            ->first();
    }

    /**
     * Create a new address
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            // If this is set as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default']) {
                Address::where('user_id', $data['user_id'])
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // If this is set as billing, unset other billing addresses
            if (isset($data['is_billing']) && $data['is_billing']) {
                Address::where('user_id', $data['user_id'])
                    ->where('is_billing', true)
                    ->update(['is_billing' => false]);
            }

            // If this is set as shipping, unset other shipping addresses
            if (isset($data['is_shipping']) && $data['is_shipping']) {
                Address::where('user_id', $data['user_id'])
                    ->where('is_shipping', true)
                    ->update(['is_shipping' => false]);
            }

            $address = Address::create($data);
            
            DB::commit();
            return $address;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an address
     */
    public function update(Address $address, array $data)
    {
        DB::beginTransaction();
        
        try {
            // If this is set as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default'] && !$address->is_default) {
                Address::where('user_id', $address->user_id)
                    ->where('is_default', true)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            // If this is set as billing, unset other billing addresses
            if (isset($data['is_billing']) && $data['is_billing'] && !$address->is_billing) {
                Address::where('user_id', $address->user_id)
                    ->where('is_billing', true)
                    ->where('id', '!=', $address->id)
                    ->update(['is_billing' => false]);
            }

            // If this is set as shipping, unset other shipping addresses
            if (isset($data['is_shipping']) && $data['is_shipping'] && !$address->is_shipping) {
                Address::where('user_id', $address->user_id)
                    ->where('is_shipping', true)
                    ->where('id', '!=', $address->id)
                    ->update(['is_shipping' => false]);
            }

            $address->update($data);
            
            DB::commit();
            return $address;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an address
     */
    public function delete(Address $address)
    {
        // Check if this is the only address
        $userAddressCount = Address::where('user_id', $address->user_id)->count();
        
        if ($userAddressCount === 1) {
            throw new \Exception('Kullanıcının en az bir adresi olmalıdır.');
        }

        // If this was default, set another as default
        if ($address->is_default) {
            $nextAddress = Address::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->first();
            
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return $address->delete();
    }

    /**
     * Set address as default
     */
    public function setAsDefault(Address $address)
    {
        DB::beginTransaction();
        
        try {
            // Unset other defaults
            Address::where('user_id', $address->user_id)
                ->where('is_default', true)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
            
            DB::commit();
            return $address;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Format address for display
     */
    public function formatAddress(Address $address)
    {
        $formatted = $address->first_name . ' ' . $address->last_name . "\n";
        $formatted .= $address->address_line_1 . "\n";
        
        if ($address->address_line_2) {
            $formatted .= $address->address_line_2 . "\n";
        }
        
        $formatted .= $address->city . ', ' . $address->state . ' ' . $address->postal_code . "\n";
        $formatted .= $address->country . "\n";
        $formatted .= 'Tel: ' . $address->phone;
        
        if ($address->email) {
            $formatted .= "\n" . 'Email: ' . $address->email;
        }
        
        return $formatted;
    }

    /**
     * Get address types
     */
    public function getAddressTypes()
    {
        return [
            'home' => 'Ev',
            'work' => 'İş',
            'billing' => 'Fatura',
            'shipping' => 'Teslimat',
            'other' => 'Diğer'
        ];
    }

    /**
     * Get countries list
     */
    public function getCountries()
    {
        return [
            'TR' => 'Türkiye',
            'US' => 'Amerika Birleşik Devletleri',
            'GB' => 'Birleşik Krallık',
            'DE' => 'Almanya',
            'FR' => 'Fransa',
            'IT' => 'İtalya',
            'ES' => 'İspanya',
            'NL' => 'Hollanda',
            'BE' => 'Belçika',
            'AT' => 'Avusturya',
            'CH' => 'İsviçre',
            // Daha fazla ülke eklenebilir
        ];
    }

    /**
     * Validate postal code format
     */
    public function validatePostalCode($postalCode, $country)
    {
        $patterns = [
            'TR' => '/^[0-9]{5}$/',
            'US' => '/^[0-9]{5}(-[0-9]{4})?$/',
            'GB' => '/^[A-Z]{1,2}[0-9]{1,2}[A-Z]?\s?[0-9][A-Z]{2}$/i',
            'DE' => '/^[0-9]{5}$/',
            'FR' => '/^[0-9]{5}$/',
        ];

        if (isset($patterns[$country])) {
            return preg_match($patterns[$country], $postalCode);
        }

        return true; // Default to valid if no pattern defined
    }
}
