<?php

namespace App\Modules\Settings\Services;

use App\Settings\GeneralSettings;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SettingsService
{
    protected $uploadService;
    protected $cacheKey = 'app_settings';
    protected $cacheTime = 86400; // 24 hours

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get all settings
     */
    public function getAll()
    {
        return Cache::remember($this->cacheKey, $this->cacheTime, function() {
            return [
                'general' => $this->getGeneralSettings(),
                'business' => $this->getBusinessSettings(),
                'social' => $this->getSocialSettings(),
                'ecommerce' => $this->getEcommerceSettings(),
                'email' => $this->getEmailSettings(),
                'maintenance' => $this->getMaintenanceSettings(),
                'analytics' => $this->getAnalyticsSettings(),
            ];
        });
    }

    /**
     * Get general settings
     */
    public function getGeneralSettings()
    {
        return [
            'site_name' => setting('general.site_name', config('app.name')),
            'site_title' => setting('general.site_title'),
            'site_description' => setting('general.site_description'),
            'site_keywords' => setting('general.site_keywords'),
            'site_logo' => setting('general.site_logo'),
            'site_favicon' => setting('general.site_favicon'),
            'admin_email' => setting('general.admin_email'),
            'support_email' => setting('general.support_email'),
            'contact_phone' => setting('general.contact_phone'),
            'contact_address' => setting('general.contact_address'),
        ];
    }

    /**
     * Get business settings
     */
    public function getBusinessSettings()
    {
        return [
            'company_name' => setting('business.company_name'),
            'tax_number' => setting('business.tax_number'),
            'registration_number' => setting('business.registration_number'),
        ];
    }

    /**
     * Get social media settings
     */
    public function getSocialSettings()
    {
        return [
            'facebook_url' => setting('social.facebook_url'),
            'twitter_url' => setting('social.twitter_url'),
            'instagram_url' => setting('social.instagram_url'),
            'linkedin_url' => setting('social.linkedin_url'),
            'youtube_url' => setting('social.youtube_url'),
        ];
    }

    /**
     * Get e-commerce settings
     */
    public function getEcommerceSettings()
    {
        return [
            'currency' => setting('ecommerce.currency', 'TRY'),
            'tax_rate' => setting('ecommerce.tax_rate', 18),
            'shipping_fee' => setting('ecommerce.shipping_fee', 0),
            'free_shipping_amount' => setting('ecommerce.free_shipping_amount', 0),
            'min_order_amount' => setting('ecommerce.min_order_amount', 0),
            'max_order_amount' => setting('ecommerce.max_order_amount', 0),
            'commission_rate' => setting('ecommerce.commission_rate', 10),
            'commission_type' => setting('ecommerce.commission_type', 'percentage'),
        ];
    }

    /**
     * Get email settings
     */
    public function getEmailSettings()
    {
        return [
            'mail_driver' => setting('email.mail_driver', 'smtp'),
            'mail_host' => setting('email.mail_host'),
            'mail_port' => setting('email.mail_port'),
            'mail_username' => setting('email.mail_username'),
            'mail_password' => setting('email.mail_password'),
            'mail_encryption' => setting('email.mail_encryption'),
            'mail_from_address' => setting('email.mail_from_address'),
            'mail_from_name' => setting('email.mail_from_name'),
        ];
    }

    /**
     * Get maintenance settings
     */
    public function getMaintenanceSettings()
    {
        return [
            'maintenance_mode' => setting('maintenance.mode', false),
            'maintenance_message' => setting('maintenance.message'),
        ];
    }

    /**
     * Get analytics settings
     */
    public function getAnalyticsSettings()
    {
        return [
            'google_analytics_id' => setting('analytics.google_analytics_id'),
            'facebook_pixel_id' => setting('analytics.facebook_pixel_id'),
            'google_recaptcha_site_key' => setting('analytics.google_recaptcha_site_key'),
            'google_recaptcha_secret_key' => setting('analytics.google_recaptcha_secret_key'),
        ];
    }

    /**
     * Update settings
     */
    public function update(array $data)
    {
        // Handle logo upload
        if (isset($data['site_logo'])) {
            $oldLogo = setting('general.site_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $upload = $this->uploadService->upload($data['site_logo'], 'settings', 'settings');
            setting(['general.site_logo' => $upload->file_path]);
            unset($data['site_logo']);
        }

        // Handle favicon upload
        if (isset($data['site_favicon'])) {
            $oldFavicon = setting('general.site_favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }
            
            $upload = $this->uploadService->upload($data['site_favicon'], 'settings', 'settings');
            setting(['general.site_favicon' => $upload->file_path]);
            unset($data['site_favicon']);
        }

        // Update general settings
        if (isset($data['site_name'])) setting(['general.site_name' => $data['site_name']]);
        if (isset($data['site_title'])) setting(['general.site_title' => $data['site_title']]);
        if (isset($data['site_description'])) setting(['general.site_description' => $data['site_description']]);
        if (isset($data['site_keywords'])) setting(['general.site_keywords' => $data['site_keywords']]);
        if (isset($data['admin_email'])) setting(['general.admin_email' => $data['admin_email']]);
        if (isset($data['support_email'])) setting(['general.support_email' => $data['support_email']]);
        if (isset($data['contact_phone'])) setting(['general.contact_phone' => $data['contact_phone']]);
        if (isset($data['contact_address'])) setting(['general.contact_address' => $data['contact_address']]);

        // Update business settings
        if (isset($data['company_name'])) setting(['business.company_name' => $data['company_name']]);
        if (isset($data['tax_number'])) setting(['business.tax_number' => $data['tax_number']]);
        if (isset($data['registration_number'])) setting(['business.registration_number' => $data['registration_number']]);

        // Update social settings
        if (isset($data['facebook_url'])) setting(['social.facebook_url' => $data['facebook_url']]);
        if (isset($data['twitter_url'])) setting(['social.twitter_url' => $data['twitter_url']]);
        if (isset($data['instagram_url'])) setting(['social.instagram_url' => $data['instagram_url']]);
        if (isset($data['linkedin_url'])) setting(['social.linkedin_url' => $data['linkedin_url']]);
        if (isset($data['youtube_url'])) setting(['social.youtube_url' => $data['youtube_url']]);

        // Update e-commerce settings
        if (isset($data['currency'])) setting(['ecommerce.currency' => $data['currency']]);
        if (isset($data['tax_rate'])) setting(['ecommerce.tax_rate' => $data['tax_rate']]);
        if (isset($data['shipping_fee'])) setting(['ecommerce.shipping_fee' => $data['shipping_fee']]);
        if (isset($data['free_shipping_amount'])) setting(['ecommerce.free_shipping_amount' => $data['free_shipping_amount']]);
        if (isset($data['min_order_amount'])) setting(['ecommerce.min_order_amount' => $data['min_order_amount']]);
        if (isset($data['max_order_amount'])) setting(['ecommerce.max_order_amount' => $data['max_order_amount']]);
        if (isset($data['commission_rate'])) setting(['ecommerce.commission_rate' => $data['commission_rate']]);
        if (isset($data['commission_type'])) setting(['ecommerce.commission_type' => $data['commission_type']]);

        // Update email settings
        if (isset($data['mail_driver'])) setting(['email.mail_driver' => $data['mail_driver']]);
        if (isset($data['mail_host'])) setting(['email.mail_host' => $data['mail_host']]);
        if (isset($data['mail_port'])) setting(['email.mail_port' => $data['mail_port']]);
        if (isset($data['mail_username'])) setting(['email.mail_username' => $data['mail_username']]);
        if (isset($data['mail_password'])) setting(['email.mail_password' => $data['mail_password']]);
        if (isset($data['mail_encryption'])) setting(['email.mail_encryption' => $data['mail_encryption']]);
        if (isset($data['mail_from_address'])) setting(['email.mail_from_address' => $data['mail_from_address']]);
        if (isset($data['mail_from_name'])) setting(['email.mail_from_name' => $data['mail_from_name']]);

        // Update maintenance settings
        if (isset($data['maintenance_mode'])) {
            setting(['maintenance.mode' => $data['maintenance_mode']]);
            
            // Toggle maintenance mode
            if ($data['maintenance_mode']) {
                Artisan::call('down', [
                    '--message' => $data['maintenance_message'] ?? 'Sistem bakımda',
                ]);
            } else {
                Artisan::call('up');
            }
        }
        if (isset($data['maintenance_message'])) setting(['maintenance.message' => $data['maintenance_message']]);

        // Update analytics settings
        if (isset($data['google_analytics_id'])) setting(['analytics.google_analytics_id' => $data['google_analytics_id']]);
        if (isset($data['facebook_pixel_id'])) setting(['analytics.facebook_pixel_id' => $data['facebook_pixel_id']]);
        if (isset($data['google_recaptcha_site_key'])) setting(['analytics.google_recaptcha_site_key' => $data['google_recaptcha_site_key']]);
        if (isset($data['google_recaptcha_secret_key'])) setting(['analytics.google_recaptcha_secret_key' => $data['google_recaptcha_secret_key']]);

        // Clear cache
        $this->clearCache();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['settings' => $data])
            ->log('Ayarlar güncellendi');

        return true;
    }

    /**
     * Clear settings cache
     */
    protected function clearCache()
    {
        Cache::forget($this->cacheKey);
        Cache::tags(['settings'])->flush();
    }

    /**
     * Get setting by key
     */
    public function get($key, $default = null)
    {
        return setting($key, $default);
    }

    /**
     * Set setting by key
     */
    public function set($key, $value)
    {
        setting([$key => $value]);
        $this->clearCache();
    }

    /**
     * Export settings
     */
    public function export()
    {
        return [
            'settings' => $this->getAll(),
            'exported_at' => now()->toDateTimeString(),
            'exported_by' => auth()->user()->name,
        ];
    }

    /**
     * Import settings
     */
    public function import(array $data)
    {
        if (!isset($data['settings'])) {
            throw new \Exception('Invalid import data format');
        }

        foreach ($data['settings'] as $group => $settings) {
            foreach ($settings as $key => $value) {
                setting(["{$group}.{$key}" => $value]);
            }
        }

        $this->clearCache();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['imported_data' => $data])
            ->log('Ayarlar içe aktarıldı');

        return true;
    }

    /**
     * Reset settings to defaults
     */
    public function resetToDefaults()
    {
        // Clear all settings
        \DB::table('settings')->truncate();

        // Set default values
        $defaults = [
            'general.site_name' => config('app.name'),
            'general.site_title' => config('app.name'),
            'general.admin_email' => 'admin@example.com',
            'general.support_email' => 'support@example.com',
            'ecommerce.currency' => 'TRY',
            'ecommerce.tax_rate' => 18,
            'ecommerce.commission_rate' => 10,
            'ecommerce.commission_type' => 'percentage',
            'email.mail_driver' => 'smtp',
            'email.mail_port' => 587,
            'email.mail_encryption' => 'tls',
        ];

        foreach ($defaults as $key => $value) {
            setting([$key => $value]);
        }

        $this->clearCache();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->log('Ayarlar varsayılana döndürüldü');

        return true;
    }
}
