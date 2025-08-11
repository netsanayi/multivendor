# 🔧 User ve Role Model Hatası Çözümü

## ❌ Karşılaşılan Hatalar
1. `Class "App\Models\User" not found`
2. `Class "App\Modules\Roles\Models\Role" not found`

## ✅ Yapılan Düzeltmeler

### 1. User Model Taşındı ve Güncellendi
**Eski Konum:** `app/Modules/Users/Models/User.php`  
**Yeni Konum:** `app/Models/User.php`

#### Yapılan Değişiklikler:
- Namespace `App\Models` olarak güncellendi
- `name` attribute desteği eklendi (Laravel varsayılan)
- `getName()` ve `setName()` metodları eklendi
- Tüm ilişkiler korundu ve yenileri eklendi

### 2. Role Model Oluşturuldu
**Konum:** `app/Modules/Roles/Models/Role.php`

#### Özellikler:
- Spatie Permission paketinden extend edildi
- Özel metodlar eklendi (getUserCount, canBeDeleted, toggleStatus)
- Gruplu permission yönetimi eklendi

### 3. Config Dosyası Güncellendi
**Dosya:** `config/auth.php`

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class, // Güncellendi
    ],
],
```

### 4. Migration Eklendi
**Dosya:** `database/migrations/2025_02_01_000001_add_name_field_to_users_table.php`

Users tablosuna `name` alanı eklendi (Laravel uyumluluğu için).

### 5. Factory ve Seeder'lar Oluşturuldu
- **UserFactory:** `database/factories/UserFactory.php`
- **RoleSeeder:** `database/seeders/RoleSeeder.php`

## 🚀 Kurulum Adımları

### Otomatik Kurulum
Terminal'de şu komutu çalıştırın:

```bash
fix_model_issue.bat
```

### Manuel Kurulum

1. **Composer Autoload'ı Yenileyin:**
```bash
composer dump-autoload --optimize
```

2. **Cache'leri Temizleyin:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

3. **Migration'ları Çalıştırın:**
```bash
php artisan migrate
```

4. **Rolleri ve İzinleri Oluşturun:**
```bash
php artisan db:seed --class=RoleSeeder
```

## 📋 Model Konumları

| Model | Namespace | Dosya Yolu |
|-------|-----------|------------|
| User | `App\Models\User` | `app/Models/User.php` |
| Role | `App\Modules\Roles\Models\Role` | `app/Modules/Roles/Models/Role.php` |
| Permission | `Spatie\Permission\Models\Permission` | Vendor klasöründe |

## 🔑 Varsayılan Roller

Sistem aşağıdaki rolleri otomatik oluşturur:

1. **super-admin:** Tüm yetkiler
2. **admin:** Yönetici yetkileri
3. **vendor:** Satıcı yetkileri
4. **customer:** Müşteri yetkileri

## 📝 Kullanım Örnekleri

### User Model Kullanımı
```php
use App\Models\User;

// Yeni kullanıcı oluştur
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
]);

// Rol ata
$user->assignRole('vendor');

// Kullanıcı kontrolü
if ($user->isVendor()) {
    // Vendor işlemleri
}
```

### Role Model Kullanımı
```php
use App\Modules\Roles\Models\Role;

// Rol oluştur
$role = Role::create([
    'name' => 'editor',
    'guard_name' => 'web',
]);

// Permission ata
$role->givePermissionTo('products.edit');

// Kullanıcı sayısını al
$userCount = $role->getUserCount();
```

## 🔍 Test Komutları

Model'lerin doğru yüklendiğini test etmek için:

```bash
php artisan tinker
>>> class_exists('App\Models\User')
>>> class_exists('App\Modules\Roles\Models\Role')
```

## ⚠️ Dikkat Edilmesi Gerekenler

1. **Namespace Değişikliği:** Eski kodlarda `App\Modules\Users\Models\User` kullanılıyorsa `App\Models\User` olarak güncelleyin.

2. **Name Alanı:** Users tablosunda artık `name` alanı var. Bu alan `first_name` ve `last_name` alanlarının birleşimi olarak otomatik doldurulur.

3. **Permission Cache:** Rol ve izin değişikliklerinden sonra cache'i temizleyin:
```bash
php artisan permission:cache-reset
```

## 📊 Model İlişkileri

### User Model İlişkileri
- `addresses()` - Kullanıcı adresleri
- `vendorProducts()` - Satıcı ürünleri
- `blogs()` - Blog yazıları
- `wishlists()` - Favori listeleri
- `tickets()` - Destek talepleri
- `messageThreads()` - Mesaj konuşmaları
- `vendorEarnings()` - Satıcı kazançları
- `vendorPayouts()` - Satıcı ödemeleri

### Role Model İlişkileri
- `users()` - Role sahip kullanıcılar
- `permissions()` - Rol izinleri

## 🎯 Sorun Giderme

### Hata: Class still not found
```bash
composer dump-autoload
php artisan optimize:clear
```

### Hata: Table users doesn't have name column
```bash
php artisan migrate
```

### Hata: Role not found
```bash
php artisan db:seed --class=RoleSeeder
```

### Hata: Permission denied
```bash
php artisan permission:cache-reset
```

---

**Sorun Çözüldü:** ✅

Model'ler artık doğru konumlarda ve tamamen fonksiyonel durumda!
