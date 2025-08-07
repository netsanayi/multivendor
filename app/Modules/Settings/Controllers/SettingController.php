<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = $this->getGroupedSettings();
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['settings'] as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Logo yükleme
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'app_logo'],
                    ['value' => asset('storage/' . $logoPath)]
                );
            }

            // Favicon yükleme
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('settings', 'public');
                Setting::updateOrCreate(
                    ['key' => 'app_favicon'],
                    ['value' => asset('storage/' . $faviconPath)]
                );
            }

            // Cache'i temizle
            Cache::forget('settings');

            // Log aktiviteyi kaydet
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['settings' => $validated['settings']])
                ->log('Ayarlar güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Ayarlar başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ayarlar güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Gruplandırılmış ayarları getir
     */
    private function getGroupedSettings()
    {
        return Cache::remember('settings', 3600, function () {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
            
            return [
                'general' => [
                    'app_name' => $settings['app_name'] ?? config('app.name'),
                    'app_description' => $settings['app_description'] ?? '',
                    'app_logo' => $settings['app_logo'] ?? '',
                    'app_favicon' => $settings['app_favicon'] ?? '',
                    'maintenance_mode' => $settings['maintenance_mode'] ?? '0',
                ],
                'contact' => [
                    'contact_email' => $settings['contact_email'] ?? '',
                    'contact_phone' => $settings['contact_phone'] ?? '',
                    'contact_address' => $settings['contact_address'] ?? '',
                ],
                'social' => [
                    'social_facebook' => $settings['social_facebook'] ?? '',
                    'social_twitter' => $settings['social_twitter'] ?? '',
                    'social_instagram' => $settings['social_instagram'] ?? '',
                    'social_youtube' => $settings['social_youtube'] ?? '',
                    'social_linkedin' => $settings['social_linkedin'] ?? '',
                ],
                'seo' => [
                    'seo_title' => $settings['seo_title'] ?? '',
                    'seo_description' => $settings['seo_description'] ?? '',
                    'seo_keywords' => $settings['seo_keywords'] ?? '',
                    'google_analytics' => $settings['google_analytics'] ?? '',
                ],
                'email' => [
                    'mail_driver' => $settings['mail_driver'] ?? env('MAIL_DRIVER', 'smtp'),
                    'mail_host' => $settings['mail_host'] ?? env('MAIL_HOST'),
                    'mail_port' => $settings['mail_port'] ?? env('MAIL_PORT'),
                    'mail_username' => $settings['mail_username'] ?? env('MAIL_USERNAME'),
                    'mail_password' => $settings['mail_password'] ?? env('MAIL_PASSWORD'),
                    'mail_encryption' => $settings['mail_encryption'] ?? env('MAIL_ENCRYPTION'),
                    'mail_from_address' => $settings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS'),
                    'mail_from_name' => $settings['mail_from_name'] ?? env('MAIL_FROM_NAME'),
                ],
                'marketplace' => [
                    'commission_rate' => $settings['commission_rate'] ?? '10',
                    'auto_approve_products' => $settings['auto_approve_products'] ?? '0',
                    'vendor_registration' => $settings['vendor_registration'] ?? '1',
                    'min_payout_amount' => $settings['min_payout_amount'] ?? '100',
                ],
                'security' => [
                    'enable_2fa' => $settings['enable_2fa'] ?? '0',
                    'password_min_length' => $settings['password_min_length'] ?? '8',
                    'password_require_uppercase' => $settings['password_require_uppercase'] ?? '1',
                    'password_require_numbers' => $settings['password_require_numbers'] ?? '1',
                    'password_require_symbols' => $settings['password_require_symbols'] ?? '1',
                    'max_login_attempts' => $settings['max_login_attempts'] ?? '5',
                    'lockout_duration' => $settings['lockout_duration'] ?? '60',
                ],
            ];
        });
    }

    /**
     * Test email settings
     */
    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Mail::raw('This is a test email from ' . config('app.name'), function ($message) use ($validated) {
                $message->to($validated['test_email'])
                        ->subject('Test Email');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test e-postası başarıyla gönderildi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'E-posta gönderilemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            // Log aktiviteyi kaydet
            activity()
                ->causedBy(auth()->user())
                ->log('Önbellek temizlendi');

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Önbellek başarıyla temizlendi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Önbellek temizlenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Optimize application
     */
    public function optimize()
    {
        try {
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            \Artisan::call('view:cache');
            \Artisan::call('optimize');

            // Log aktiviteyi kaydet
            activity()
                ->causedBy(auth()->user())
                ->log('Uygulama optimize edildi');

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Uygulama başarıyla optimize edildi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Optimizasyon sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }
}
