# Laravel Multi-Vendor Marketplace

Laravel 12 ve PostgreSQL kullanılarak geliştirilmiş çok satıcılı pazaryeri uygulaması.

## 🚀 Özellikler

### Temel E-ticaret Modülleri
- **Kategoriler**: Hiyerarşik kategori yapısı
- **Ürünler**: Merkezi ürün kataloğu yönetimi
- **Müşteri Ürünleri**: Satıcıya özel ürün listeleme
- **Ürün Özellikleri**: Dinamik özellik yönetimi
- **Markalar**: Marka yönetimi
- **Yüklemeler**: Merkezi medya dosya yönetimi

### Kullanıcı ve Erişim Yönetimi
- **Kullanıcılar**: Müşteri, satıcı ve yönetici hesapları
- **Roller ve İzinler**: Detaylı yetkilendirme sistemi
- **Adresler**: Kullanıcı adres yönetimi

### Sistem ve Yardımcı Modüller
- **Aktivite Logu**: Tüm işlemlerin kaydı
- **Çoklu Para Birimi**: Farklı para birimleri desteği
- **Çoklu Dil**: Türkçe ve İngilizce dil desteği
- **Banner Yönetimi**: Ana sayfa banner yönetimi
- **Blog**: İçerik yönetim sistemi
- **Ayarlar**: Dinamik sistem ayarları

## 📋 Gereksinimler

- PHP 8.3.16 veya üzeri
- PostgreSQL 14 veya üzeri
- Composer 2.x
- Node.js 18.x ve NPM

## 🛠️ Kurulum

1. **Projeyi klonlayın**
   ```bash
   cd C:\Users\Murat\Projects\laravelmv
   ```

2. **Bağımlılıkları yükleyin**
   ```bash
   composer install
   npm install
   ```

3. **Ortam dosyasını oluşturun**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Veritabanını yapılandırın**
   
   `.env` dosyasında PostgreSQL ayarlarını düzenleyin:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=laravelmv
   DB_USERNAME=postgres
   DB_PASSWORD=postgres
   ```

5. **Migration'ları çalıştırın**
   ```bash
   php artisan migrate
   ```

6. **Storage linkini oluşturun**
   ```bash
   php artisan storage:link
   ```

7. **Frontend varlıklarını derleyin**
   ```bash
   npm run build
   ```

8. **Uygulamayı başlatın**
   ```bash
   php artisan serve
   ```

## 🏗️ Proje Yapısı

```
laravelmv/
├── app/
│   ├── Modules/           # Modüler yapı
│   │   ├── Categories/    # Kategori modülü
│   │   ├── Products/      # Ürün modülü
│   │   ├── Users/         # Kullanıcı modülü
│   │   └── ...           # Diğer modüller
│   └── ...
├── database/
│   ├── migrations/        # Veritabanı migration'ları
│   └── ...
├── routes/
│   └── web.php           # Web rotaları
└── ...
```

## 🔐 Güvenlik Özellikleri

- İki faktörlü kimlik doğrulama (2FA)
- Güçlü şifre politikaları
- Rate limiting ile brute force koruması
- CSRF ve XSS koruması
- Güvenli oturum yönetimi

## 📦 Kullanılan Paketler

- **laravel/jetstream**: Kullanıcı yönetimi ve kimlik doğrulama
- **spatie/laravel-medialibrary**: Medya dosya yönetimi
- **spatie/laravel-permission**: Rol ve izin yönetimi
- **spatie/laravel-activitylog**: Aktivite loglama
- **spatie/laravel-sluggable**: SEO-friendly URL'ler
- **spatie/laravel-translatable**: Çoklu dil desteği
- **akaunting/laravel-money**: Para birimi yönetimi

## 🤝 Katkıda Bulunma

1. Bu repoyu fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 📞 İletişim

Proje ile ilgili sorularınız için issue açabilirsiniz.
