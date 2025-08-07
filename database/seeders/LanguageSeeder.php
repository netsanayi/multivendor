<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Languages\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'tr',
                'name' => 'Türkçe',
                'locale' => 'tr_TR.UTF-8',
                'order' => 1,
                'is_rtl' => false,
                'status' => true,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'locale' => 'en_US.UTF-8',
                'order' => 2,
                'is_rtl' => false,
                'status' => true,
            ],
            [
                'code' => 'ar',
                'name' => 'العربية',
                'locale' => 'ar_SA.UTF-8',
                'order' => 3,
                'is_rtl' => true,
                'status' => false,
            ],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Diller oluşturuldu.');
    }
}
