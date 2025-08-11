<?php
// Migration dosyalarını bağımlılık sırasına göre yeniden adlandırma

$migrations_path = __DIR__ . '/database/migrations/';

// Yeniden adlandırma haritası [eski_ad => yeni_ad]
$rename_map = [
    // 1. BAĞIMSIZ TABLOLAR (0001-0010)
    '2025_01_01_000001_create_uploads_table.php' => '2025_01_01_000001_create_uploads_table.php', // zaten doğru
    'temp_categories.php' => '2025_01_01_000002_create_categories_table.php',
    '2025_01_01_000002_create_currencies_table.php' => '2025_01_01_000003_create_currencies_table.php',
    '2025_01_01_000003_create_brands_table.php' => '2025_01_01_000004_create_brands_table.php',
    '2025_01_01_000011_create_languages_table.php' => '2025_01_01_000005_create_languages_table.php',
    '2025_01_01_000012_create_banners_table.php' => '2025_01_01_000006_create_banners_table.php',
    '2025_01_01_000007_create_attribute_categories_table.php' => '2025_01_01_000007_create_attribute_categories_table.php', // zaten doğru
    '2022_12_14_083707_create_settings_table.php' => '2025_01_01_000008_create_settings_table.php',
    
    // 2. USERS TABLOSU (currencies'e bağımlı) - 0020
    '2025_01_01_000005_create_users_table.php' => '2025_01_01_000020_create_users_table.php',
    
    // 3. USERS'A BAĞIMLI TABLOLAR - 0021-0029
    '2014_10_12_200000_add_two_factor_columns_to_users_table.php' => '2025_01_01_000021_add_two_factor_columns_to_users_table.php',
    '2019_12_14_000001_create_personal_access_tokens_table.php' => '2025_01_01_000022_create_personal_access_tokens_table.php',
    '2025_01_01_000010_create_addresses_table.php' => '2025_01_01_000023_create_addresses_table.php',
    '2025_01_01_000009_create_blogs_table.php' => '2025_01_01_000024_create_blogs_table.php',
    
    // 4. PRODUCTS (categories, brands, currencies'e bağımlı) - 0030
    '2025_01_01_000004_create_products_table.php' => '2025_01_01_000030_create_products_table.php',
    '2025_01_01_000008_create_product_attributes_table.php' => '2025_01_01_000031_create_product_attributes_table.php',
    
    // 5. VENDOR PRODUCTS (users, products, currencies'e bağımlı) - 0040
    '2025_01_01_000006_create_vendor_products_table.php' => '2025_01_01_000040_create_vendor_products_table.php',
    
    // 6. VENDOR İLİŞKİLİ TABLOLAR - 0041-0049
    '2025_01_30_000001_create_vendor_commissions_table.php' => '2025_01_01_000041_create_vendor_commissions_table.php',
    '2025_01_30_000002_create_vendor_earnings_table.php' => '2025_01_01_000042_create_vendor_earnings_table.php',
    '2025_01_30_000003_create_vendor_payouts_table.php' => '2025_01_01_000043_create_vendor_payouts_table.php',
    '2025_01_30_000004_create_wishlists_table.php' => '2025_01_01_000044_create_wishlists_table.php',
    
    // 7. DİĞER MODÜLLER - 0050+
    '2025_01_31_000001_create_tickets_tables.php' => '2025_01_01_000050_create_tickets_tables.php',
    '2025_01_31_000002_create_messages_tables.php' => '2025_01_01_000051_create_messages_tables.php',
    '2025_01_31_000003_create_notifications_tables.php' => '2025_01_01_000052_create_notifications_tables.php',
    
    // 8. SPATIE PAKETLERİ - 0060+
    '2025_08_07_233217_create_activity_log_table.php' => '2025_01_01_000060_create_activity_log_table.php',
    '2025_08_07_233218_add_event_column_to_activity_log_table.php' => '2025_01_01_000061_add_event_column_to_activity_log_table.php',
    '2025_08_07_233219_add_batch_uuid_column_to_activity_log_table.php' => '2025_01_01_000062_add_batch_uuid_column_to_activity_log_table.php',
    '2025_08_07_234213_create_permission_tables.php' => '2025_01_01_000063_create_permission_tables.php',
    '2025_08_07_234221_create_media_table.php' => '2025_01_01_000064_create_media_table.php',
];

echo "Migration dosyaları yeniden adlandırılıyor...\n\n";

$success_count = 0;
$error_count = 0;

foreach ($rename_map as $old_name => $new_name) {
    $old_path = $migrations_path . $old_name;
    $new_path = $migrations_path . $new_name;
    
    if (file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            echo "✓ $old_name -> $new_name\n";
            $success_count++;
        } else {
            echo "✗ HATA: $old_name yeniden adlandırılamadı\n";
            $error_count++;
        }
    } else if ($old_name != $new_name) {
        echo "- ATLA: $old_name bulunamadı\n";
    }
}

echo "\n========================================\n";
echo "İşlem tamamlandı!\n";
echo "Başarılı: $success_count dosya\n";
echo "Hata: $error_count dosya\n";
echo "\nŞimdi şu komutu çalıştırabilirsiniz:\n";
echo "php artisan migrate:fresh --seed\n";
