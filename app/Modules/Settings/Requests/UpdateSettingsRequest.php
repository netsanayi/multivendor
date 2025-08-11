<?php

namespace App\Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('settings.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // General Settings
            'site_name' => 'nullable|string|max:255',
            'site_title' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:512',
            'admin_email' => 'nullable|email|max:255',
            'support_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            
            // Business Settings
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            
            // Social Media
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            
            // E-commerce Settings
            'currency' => 'nullable|string|max:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'shipping_fee' => 'nullable|numeric|min:0',
            'free_shipping_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|min:0',
            
            // Commission Settings
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|in:percentage,fixed',
            
            // Email Settings
            'mail_driver' => 'nullable|string|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            
            // Maintenance Mode
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            
            // Other Settings
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'google_recaptcha_site_key' => 'nullable|string|max:255',
            'google_recaptcha_secret_key' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'site_logo.image' => 'Logo bir resim dosyası olmalıdır.',
            'site_logo.max' => 'Logo boyutu maksimum 2MB olmalıdır.',
            'site_favicon.image' => 'Favicon bir resim dosyası olmalıdır.',
            'site_favicon.max' => 'Favicon boyutu maksimum 512KB olmalıdır.',
            'admin_email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'support_email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'facebook_url.url' => 'Geçerli bir URL giriniz.',
            'twitter_url.url' => 'Geçerli bir URL giriniz.',
            'instagram_url.url' => 'Geçerli bir URL giriniz.',
            'linkedin_url.url' => 'Geçerli bir URL giriniz.',
            'youtube_url.url' => 'Geçerli bir URL giriniz.',
            'tax_rate.numeric' => 'Vergi oranı sayısal bir değer olmalıdır.',
            'tax_rate.min' => 'Vergi oranı 0\'dan küçük olamaz.',
            'tax_rate.max' => 'Vergi oranı 100\'den büyük olamaz.',
            'commission_rate.numeric' => 'Komisyon oranı sayısal bir değer olmalıdır.',
            'commission_rate.min' => 'Komisyon oranı 0\'dan küçük olamaz.',
            'commission_rate.max' => 'Komisyon oranı 100\'den büyük olamaz.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean
        $this->merge([
            'maintenance_mode' => $this->maintenance_mode ? true : false,
        ]);
    }
}
