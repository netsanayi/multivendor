# Laravel Multi-Vendor Marketplace Kurulum Kılavuzu

## Gereksinimler
- PHP 8.3.16 veya üzeri (C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64)
- Composer
- PostgreSQL
- Node.js & NPM

## 1. Laravel Kurulumu

```bash
cd C:\Users\Murat\Projects\laravelmv
composer create-project --prefer-dist laravel/laravel . "^12.0"
```

## 2. PostgreSQL Veritabanı Yapılandırması

.env dosyasındaki veritabanı ayarlarını aşağıdaki gibi düzenleyin:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravelmv
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

## 3. Gerekli Paketlerin Kurulumu

```bash
# Jetstream (Kullanıcı Yönetimi)
composer require laravel/jetstream
php artisan jetstream:install livewire

# Spatie Medya Kütüphanesi (Yüklemeler Modülü)
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

# Spatie İzin Yönetimi (Roller Modülü)
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Spatie Aktivite Logu (Log Sistemi)
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# Spatie Sluggable (SEO-friendly URL'ler)
composer require spatie/laravel-sluggable

# Spatie Translatable (Çoklu Dil)
composer require spatie/laravel-translatable

# Para Birimi Yönetimi
composer require akaunting/laravel-money
php artisan vendor:publish --tag="money"

# İki Faktörlü Kimlik Doğrulama
composer require pragmarx/google2fa-laravel
php artisan vendor:publish --provider="PragmARX\Google2FALaravel\ServiceProvider"

# Dinamik Ayarlar Yönetimi
composer require spatie/laravel-settings
```

## 4. Migration'ların Çalıştırılması

```bash
php artisan migrate
```

## 5. Frontend Kurulumu

```bash
npm install
npm run build
```

## 6. Storage Link Oluşturma

```bash
php artisan storage:link
```

## 7. Modüler Yapı Klasörlerinin Oluşturulması

```bash
mkdir app\Modules
mkdir app\Modules\Categories
mkdir app\Modules\Products
mkdir app\Modules\VendorProducts
mkdir app\Modules\ProductAttributes
mkdir app\Modules\AttributeCategories
mkdir app\Modules\Brands
mkdir app\Modules\Uploads
mkdir app\Modules\Blogs
mkdir app\Modules\Users
mkdir app\Modules\Roles
mkdir app\Modules\Addresses
mkdir app\Modules\ActivityLog
mkdir app\Modules\Currencies
mkdir app\Modules\Languages
mkdir app\Modules\Banners
mkdir app\Modules\Settings
```

## 8. Composer Autoload Güncelleme

composer.json dosyasına PSR-4 autoloading ekleyin:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "App\\Modules\\": "app/Modules/"
    }
}
```

Ardından:
```bash
composer dump-autoload
```

## 9. Projeyi Çalıştırma

```bash
php artisan serve
```

Tarayıcıda http://localhost:8000 adresini açın.
