@echo off
echo Migration dosyalari yeniden adlandiriliyor...
cd database\migrations

:: 1. BAGIMSIZ TABLOLAR (0001-0010)
if exist "2025_01_01_000000_create_uploads_table.php" ren "2025_01_01_000000_create_uploads_table.php" "2025_01_01_000001_create_uploads_table.php"
if exist "2025_01_01_000001_create_categories_table.php" ren "2025_01_01_000001_create_categories_table.php" "2025_01_01_000002_create_categories_table.php"
if exist "2025_01_01_000002_create_currencies_table.php" ren "2025_01_01_000002_create_currencies_table.php" "2025_01_01_000003_create_currencies_table.php"
if exist "2025_01_01_000003_create_brands_table.php" ren "2025_01_01_000003_create_brands_table.php" "2025_01_01_000004_create_brands_table.php"
if exist "2025_01_01_000011_create_languages_table.php" ren "2025_01_01_000011_create_languages_table.php" "2025_01_01_000005_create_languages_table.php"
if exist "2025_01_01_000012_create_banners_table.php" ren "2025_01_01_000012_create_banners_table.php" "2025_01_01_000006_create_banners_table.php"
if exist "2022_12_14_083707_create_settings_table.php" ren "2022_12_14_083707_create_settings_table.php" "2025_01_01_000008_create_settings_table.php"

:: 2. USERS TABLOSU (currencies'e bagimlı) - 0020
if exist "2025_01_01_000005_create_users_table.php" ren "2025_01_01_000005_create_users_table.php" "2025_01_01_000020_create_users_table.php"

:: 3. USERS'A BAGIMLI TABLOLAR - 0021-0029
if exist "2014_10_12_200000_add_two_factor_columns_to_users_table.php" ren "2014_10_12_200000_add_two_factor_columns_to_users_table.php" "2025_01_01_000021_add_two_factor_columns_to_users_table.php"
if exist "2019_12_14_000001_create_personal_access_tokens_table.php" ren "2019_12_14_000001_create_personal_access_tokens_table.php" "2025_01_01_000022_create_personal_access_tokens_table.php"
if exist "2025_01_01_000010_create_addresses_table.php" ren "2025_01_01_000010_create_addresses_table.php" "2025_01_01_000023_create_addresses_table.php"
if exist "2025_01_01_000009_create_blogs_table.php" ren "2025_01_01_000009_create_blogs_table.php" "2025_01_01_000024_create_blogs_table.php"

:: 4. PRODUCTS (categories, brands, currencies'e bagimlı) - 0030
if exist "2025_01_01_000004_create_products_table.php" ren "2025_01_01_000004_create_products_table.php" "2025_01_01_000030_create_products_table.php"
if exist "2025_01_01_000008_create_product_attributes_table.php" ren "2025_01_01_000008_create_product_attributes_table.php" "2025_01_01_000031_create_product_attributes_table.php"

:: 5. VENDOR PRODUCTS (users, products, currencies'e bagimlı) - 0040
if exist "2025_01_01_000006_create_vendor_products_table.php" ren "2025_01_01_000006_create_vendor_products_table.php" "2025_01_01_000040_create_vendor_products_table.php"

:: 6. VENDOR ILISKILI TABLOLAR - 0041-0049
if exist "2025_01_30_000001_create_vendor_commissions_table.php" ren "2025_01_30_000001_create_vendor_commissions_table.php" "2025_01_01_000041_create_vendor_commissions_table.php"
if exist "2025_01_30_000002_create_vendor_earnings_table.php" ren "2025_01_30_000002_create_vendor_earnings_table.php" "2025_01_01_000042_create_vendor_earnings_table.php"
if exist "2025_01_30_000003_create_vendor_payouts_table.php" ren "2025_01_30_000003_create_vendor_payouts_table.php" "2025_01_01_000043_create_vendor_payouts_table.php"
if exist "2025_01_30_000004_create_wishlists_table.php" ren "2025_01_30_000004_create_wishlists_table.php" "2025_01_01_000044_create_wishlists_table.php"

:: 7. DIGER MODULLER - 0050+
if exist "2025_01_31_000001_create_tickets_tables.php" ren "2025_01_31_000001_create_tickets_tables.php" "2025_01_01_000050_create_tickets_tables.php"
if exist "2025_01_31_000002_create_messages_tables.php" ren "2025_01_31_000002_create_messages_tables.php" "2025_01_01_000051_create_messages_tables.php"
if exist "2025_01_31_000003_create_notifications_tables.php" ren "2025_01_31_000003_create_notifications_tables.php" "2025_01_01_000052_create_notifications_tables.php"

:: 8. SPATIE PAKETLERI - 0060+
if exist "2025_08_07_233217_create_activity_log_table.php" ren "2025_08_07_233217_create_activity_log_table.php" "2025_01_01_000060_create_activity_log_table.php"
if exist "2025_08_07_233218_add_event_column_to_activity_log_table.php" ren "2025_08_07_233218_add_event_column_to_activity_log_table.php" "2025_01_01_000061_add_event_column_to_activity_log_table.php"
if exist "2025_08_07_233219_add_batch_uuid_column_to_activity_log_table.php" ren "2025_08_07_233219_add_batch_uuid_column_to_activity_log_table.php" "2025_01_01_000062_add_batch_uuid_column_to_activity_log_table.php"
if exist "2025_08_07_234213_create_permission_tables.php" ren "2025_08_07_234213_create_permission_tables.php" "2025_01_01_000063_create_permission_tables.php"
if exist "2025_08_07_234221_create_media_table.php" ren "2025_08_07_234221_create_media_table.php" "2025_01_01_000064_create_media_table.php"

cd ..\..
echo Islem tamamlandi!
echo.
echo Simdi su komutu calistirabilirsiniz:
echo php artisan migrate:fresh --seed
pause
