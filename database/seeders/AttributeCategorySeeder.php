<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\AttributeCategories\Models\AttributeCategory;

class AttributeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributeCategories = [
            [
                'name' => 'Genel Özellikler',
                'status' => true,
            ],
            [
                'name' => 'Teknik Özellikler',
                'status' => true,
            ],
            [
                'name' => 'Fiziksel Özellikler',
                'status' => true,
            ],
            [
                'name' => 'Renk ve Desen',
                'status' => true,
            ],
            [
                'name' => 'Malzeme',
                'status' => true,
            ],
            [
                'name' => 'Performans',
                'status' => true,
            ],
            [
                'name' => 'Bağlantı',
                'status' => true,
            ],
            [
                'name' => 'Garanti ve Servis',
                'status' => true,
            ],
        ];

        foreach ($attributeCategories as $category) {
            AttributeCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Özellik kategorileri oluşturuldu.');
    }
}
