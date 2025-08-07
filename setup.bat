@echo off
echo ====================================
echo Laravel Multi-Vendor Marketplace
echo Kurulum Scripti
echo ====================================
echo.

echo [1/10] Composer bağımlılıkları yükleniyor...
call composer install
if %errorlevel% neq 0 (
    echo HATA: Composer bağımlılıkları yüklenemedi!
    pause
    exit /b %errorlevel%
)

echo.
echo [2/10] .env dosyası oluşturuluyor...
if not exist .env (
    copy .env.example .env
    echo .env dosyası oluşturuldu.
) else (
    echo .env dosyası zaten mevcut.
)

echo.
echo [3/10] Uygulama anahtarı oluşturuluyor...
call php artisan key:generate
if %errorlevel% neq 0 (
    echo HATA: Uygulama anahtarı oluşturulamadı!
    pause
    exit /b %errorlevel%
)

echo.
echo [4/10] Veritabanı bağlantısı kontrol ediliyor...
echo Lütfen PostgreSQL veritabanının çalıştığından ve
echo .env dosyasındaki ayarların doğru olduğundan emin olun.
echo.
echo DB_CONNECTION=pgsql
echo DB_HOST=127.0.0.1
echo DB_PORT=5432
echo DB_DATABASE=laravelmv
echo DB_USERNAME=postgres
echo DB_PASSWORD=postgres
echo.
pause

echo.
echo [5/10] Migration'lar çalıştırılıyor...
call php artisan migrate
if %errorlevel% neq 0 (
    echo HATA: Migration'lar çalıştırılamadı!
    echo Veritabanı ayarlarınızı kontrol edin.
    pause
    exit /b %errorlevel%
)

echo.
echo [6/10] Storage link oluşturuluyor...
call php artisan storage:link
if %errorlevel% neq 0 (
    echo HATA: Storage link oluşturulamadı!
    pause
    exit /b %errorlevel%
)

echo.
echo [7/10] NPM bağımlılıkları yükleniyor...
call npm install
if %errorlevel% neq 0 (
    echo HATA: NPM bağımlılıkları yüklenemedi!
    pause
    exit /b %errorlevel%
)

echo.
echo [8/10] Frontend varlıkları derleniyor...
call npm run build
if %errorlevel% neq 0 (
    echo HATA: Frontend varlıkları derlenemedi!
    pause
    exit /b %errorlevel%
)

echo.
echo [9/10] Cache temizleniyor...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo [10/10] Modül klasörleri oluşturuluyor...
if not exist "app\Modules\ProductAttributes" mkdir "app\Modules\ProductAttributes"
if not exist "app\Modules\AttributeCategories" mkdir "app\Modules\AttributeCategories"
if not exist "app\Modules\Brands" mkdir "app\Modules\Brands"
if not exist "app\Modules\Uploads" mkdir "app\Modules\Uploads"
if not exist "app\Modules\Blogs" mkdir "app\Modules\Blogs"
if not exist "app\Modules\Users" mkdir "app\Modules\Users"
if not exist "app\Modules\Roles" mkdir "app\Modules\Roles"
if not exist "app\Modules\Addresses" mkdir "app\Modules\Addresses"
if not exist "app\Modules\ActivityLog" mkdir "app\Modules\ActivityLog"
if not exist "app\Modules\Currencies" mkdir "app\Modules\Currencies"
if not exist "app\Modules\Languages" mkdir "app\Modules\Languages"
if not exist "app\Modules\Banners" mkdir "app\Modules\Banners"
if not exist "app\Modules\Settings" mkdir "app\Modules\Settings"

echo.
echo ====================================
echo Kurulum tamamlandı!
echo ====================================
echo.
echo Uygulamayı başlatmak için:
echo php artisan serve
echo.
echo Tarayıcınızda http://localhost:8000 adresini açın.
echo.
pause
