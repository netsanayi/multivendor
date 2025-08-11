<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $site_description;
    public string $site_email;
    public string $site_phone;
    public string $site_address;
    public bool $maintenance_mode;
    public string $currency;
    public float $commission_rate;
    public int $items_per_page;
    public bool $email_verification_required;
    public bool $vendor_auto_approve;
    
    public static function group(): string
    {
        return 'general';
    }
}
