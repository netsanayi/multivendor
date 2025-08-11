<?php

namespace App\Modules\Languages\Services;

use App\Modules\Languages\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class LanguageService
{
    /**
     * Cache key for languages
     */
    const CACHE_KEY = 'languages_list';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all languages with filters
     *
     * @param array $filters
     * @param int|null $perPage
     * @return mixed
     */
    public function getAllLanguages(array $filters = [], ?int $perPage = null)
    {
        $query = Language::query();

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('native_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('locale', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_default'])) {
            $query->where('is_default', $filters['is_default']);
        }

        if (isset($filters['direction'])) {
            $query->where('direction', $filters['direction']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'display_order';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Return paginated or all results
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get active languages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Language::where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get default language
     *
     * @return Language|null
     */
    public function getDefaultLanguage()
    {
        return Cache::remember('default_language', self::CACHE_TTL, function () {
            return Language::where('is_default', true)->first();
        });
    }

    /**
     * Get language by code
     *
     * @param string $code
     * @return Language|null
     */
    public function getLanguageByCode(string $code)
    {
        return Cache::remember("language_{$code}", self::CACHE_TTL, function () use ($code) {
            return Language::where('code', strtolower($code))->first();
        });
    }

    /**
     * Get language by locale
     *
     * @param string $locale
     * @return Language|null
     */
    public function getLanguageByLocale(string $locale)
    {
        return Cache::remember("language_locale_{$locale}", self::CACHE_TTL, function () use ($locale) {
            return Language::where('locale', $locale)->first();
        });
    }

    /**
     * Create a new language
     *
     * @param array $data
     * @return Language
     */
    public function createLanguage(array $data): Language
    {
        return DB::transaction(function () use ($data) {
            // If this is set as default, unset other defaults
            if ($data['is_default'] ?? false) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }

            // Create language
            $language = Language::create($data);

            // Create language directory if it doesn't exist
            $this->createLanguageFiles($language);

            // Clear cache
            $this->clearCache();

            return $language;
        });
    }

    /**
     * Update a language
     *
     * @param Language $language
     * @param array $data
     * @return Language
     */
    public function updateLanguage(Language $language, array $data): Language
    {
        return DB::transaction(function () use ($language, $data) {
            $oldCode = $language->code;

            // If this is set as default, unset other defaults
            if (($data['is_default'] ?? false) && !$language->is_default) {
                Language::where('is_default', true)
                    ->where('id', '!=', $language->id)
                    ->update(['is_default' => false]);
            }

            // Prevent removing default status if it's the only active language
            if ($language->is_default && !($data['is_default'] ?? true)) {
                $activeLanguagesCount = Language::where('is_active', true)->count();
                if ($activeLanguagesCount <= 1) {
                    throw new \Exception('En az bir aktif varsayılan dil olmalıdır.');
                }
            }

            // Update language
            $language->update($data);

            // If code changed, rename language directory
            if ($oldCode !== $language->code) {
                $this->renameLanguageFiles($oldCode, $language->code);
            }

            // Clear cache
            $this->clearCache();

            return $language->fresh();
        });
    }

    /**
     * Delete a language
     *
     * @param Language $language
     * @return bool
     * @throws \Exception
     */
    public function deleteLanguage(Language $language): bool
    {
        // Prevent deleting default language
        if ($language->is_default) {
            throw new \Exception('Varsayılan dil silinemez.');
        }

        // Check if language is in use
        if ($this->isLanguageInUse($language)) {
            throw new \Exception('Bu dil kullanımda olduğu için silinemez.');
        }

        $deleted = $language->delete();

        if ($deleted) {
            // Delete language files
            $this->deleteLanguageFiles($language);
            
            // Clear cache
            $this->clearCache();
        }

        return $deleted;
    }

    /**
     * Toggle language active status
     *
     * @param Language $language
     * @return Language
     * @throws \Exception
     */
    public function toggleActive(Language $language): Language
    {
        // Prevent deactivating default language
        if ($language->is_default && $language->is_active) {
            throw new \Exception('Varsayılan dil deaktif edilemez.');
        }

        $language->update(['is_active' => !$language->is_active]);
        $this->clearCache();

        return $language;
    }

    /**
     * Set language as default
     *
     * @param Language $language
     * @return Language
     * @throws \Exception
     */
    public function setAsDefault(Language $language): Language
    {
        if (!$language->is_active) {
            throw new \Exception('Aktif olmayan bir dil varsayılan olarak ayarlanamaz.');
        }

        return DB::transaction(function () use ($language) {
            // Unset other defaults
            Language::where('is_default', true)
                ->where('id', '!=', $language->id)
                ->update(['is_default' => false]);

            // Set this as default
            $language->update(['is_default' => true]);

            // Update app locale
            $this->updateAppLocale($language);

            // Clear cache
            $this->clearCache();

            return $language;
        });
    }

    /**
     * Update display order of languages
     *
     * @param array $order Array of language IDs in order
     * @return void
     */
    public function updateOrder(array $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order as $index => $languageId) {
                Language::where('id', $languageId)
                    ->update(['display_order' => $index]);
            }
        });

        $this->clearCache();
    }

    /**
     * Get available locales for the application
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        return $this->getActiveLanguages()
            ->pluck('locale')
            ->toArray();
    }

    /**
     * Get language for current session
     *
     * @return Language
     */
    public function getCurrentLanguage(): Language
    {
        $locale = app()->getLocale();
        $language = $this->getLanguageByLocale($locale);

        if (!$language) {
            $language = $this->getDefaultLanguage();
        }

        return $language;
    }

    /**
     * Set current language for session
     *
     * @param string $code
     * @return Language
     */
    public function setCurrentLanguage(string $code): Language
    {
        $language = $this->getLanguageByCode($code);

        if (!$language || !$language->is_active) {
            throw new \Exception('Geçersiz veya aktif olmayan dil.');
        }

        // Set locale
        app()->setLocale($language->locale);
        session(['locale' => $language->locale]);

        return $language;
    }

    /**
     * Create language files
     *
     * @param Language $language
     * @return void
     */
    private function createLanguageFiles(Language $language): void
    {
        $langPath = resource_path("lang/{$language->code}");

        if (!File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);

            // Copy default language files
            $defaultLangPath = resource_path('lang/en');
            if (File::exists($defaultLangPath)) {
                File::copyDirectory($defaultLangPath, $langPath);
            } else {
                // Create basic translation files
                $files = [
                    'auth.php',
                    'pagination.php',
                    'passwords.php',
                    'validation.php',
                    'messages.php'
                ];

                foreach ($files as $file) {
                    File::put($langPath . '/' . $file, "<?php\n\nreturn [\n\n];");
                }
            }

            Log::info("Language files created for: {$language->name}");
        }
    }

    /**
     * Rename language files
     *
     * @param string $oldCode
     * @param string $newCode
     * @return void
     */
    private function renameLanguageFiles(string $oldCode, string $newCode): void
    {
        $oldPath = resource_path("lang/{$oldCode}");
        $newPath = resource_path("lang/{$newCode}");

        if (File::exists($oldPath) && !File::exists($newPath)) {
            File::move($oldPath, $newPath);
            Log::info("Language files renamed from {$oldCode} to {$newCode}");
        }
    }

    /**
     * Delete language files
     *
     * @param Language $language
     * @return void
     */
    private function deleteLanguageFiles(Language $language): void
    {
        $langPath = resource_path("lang/{$language->code}");

        if (File::exists($langPath)) {
            File::deleteDirectory($langPath);
            Log::info("Language files deleted for: {$language->name}");
        }
    }

    /**
     * Update application locale
     *
     * @param Language $language
     * @return void
     */
    private function updateAppLocale(Language $language): void
    {
        // Update config
        config(['app.locale' => $language->locale]);
        
        // Update .env file if needed
        $this->updateEnvFile('APP_LOCALE', $language->locale);
    }

    /**
     * Update .env file
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function updateEnvFile(string $key, string $value): void
    {
        $path = base_path('.env');
        
        if (File::exists($path)) {
            $content = File::get($path);
            $pattern = "/^{$key}=.*/m";
            
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            
            File::put($path, $content);
            
            // Clear config cache
            Artisan::call('config:clear');
        }
    }

    /**
     * Check if language is in use
     *
     * @param Language $language
     * @return bool
     */
    private function isLanguageInUse(Language $language): bool
    {
        // Check if used in user preferences
        if (DB::table('users')->where('language_id', $language->id)->exists()) {
            return true;
        }

        // Check if used in translations
        if (DB::table('translations')->where('language_id', $language->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get language statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember('language_statistics', 3600, function () {
            return [
                'total' => Language::count(),
                'active' => Language::where('is_active', true)->count(),
                'inactive' => Language::where('is_active', false)->count(),
                'default' => Language::where('is_default', true)->value('name'),
                'rtl_languages' => Language::where('direction', 'rtl')->count(),
                'ltr_languages' => Language::where('direction', 'ltr')->count(),
                'translation_coverage' => $this->getTranslationCoverage()
            ];
        });
    }

    /**
     * Get translation coverage for each language
     *
     * @return array
     */
    private function getTranslationCoverage(): array
    {
        $coverage = [];
        $languages = Language::all();
        
        foreach ($languages as $language) {
            $langPath = resource_path("lang/{$language->code}");
            if (File::exists($langPath)) {
                $files = File::files($langPath);
                $totalKeys = 0;
                $translatedKeys = 0;
                
                foreach ($files as $file) {
                    $translations = include $file->getPathname();
                    if (is_array($translations)) {
                        $totalKeys += count($translations);
                        $translatedKeys += count(array_filter($translations, function ($value) {
                            return !empty($value);
                        }));
                    }
                }
                
                $coverage[$language->code] = $totalKeys > 0 
                    ? round(($translatedKeys / $totalKeys) * 100, 2) 
                    : 0;
            } else {
                $coverage[$language->code] = 0;
            }
        }
        
        return $coverage;
    }

    /**
     * Clear language cache
     *
     * @return void
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('default_language');
        Cache::forget('language_statistics');
        
        // Clear individual language caches
        Language::all()->each(function ($language) {
            Cache::forget("language_{$language->code}");
            Cache::forget("language_locale_{$language->locale}");
        });
    }
}
