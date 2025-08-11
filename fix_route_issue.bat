@echo off
cls
echo ===============================================
echo Laravel MV - Route Sorunu Cozumu
echo ===============================================
echo.

echo [ADIM 1] Composer autoload yenileniyor...
composer dump-autoload --optimize
if %ERRORLEVEL% NEQ 0 (
    echo HATA: Composer autoload basarisiz!
    pause
    exit /b 1
)

echo.
echo [ADIM 2] Laravel cache'leri temizleniyor...

php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

echo.
echo [ADIM 3] Laravel optimize ediliyor...
php artisan optimize:clear

echo.
echo [ADIM 4] Route'lar kontrol ediliyor...
php check_routes.php

echo.
echo [ADIM 5] Route listesi (activity-log)...
php artisan route:list --name=activity-log

echo.
echo ===============================================
echo COZUM TAMAMLANDI!
echo ===============================================
echo.
echo Simdi su adimlari deneyin:
echo.
echo 1. Tarayicinizin cache'ini temizleyin (Ctrl+F5)
echo 2. Laravel sunucusunu yeniden baslatin:
echo    php artisan serve
echo 3. Admin panele giris yapin
echo 4. http://localhost:8000/admin/activity-log adresine gidin
echo.
echo Hala sorun varsa:
echo - storage/logs/laravel.log dosyasini kontrol edin
echo - php artisan tinker ile Route::has('admin.activity-log.export') komutunu deneyin
echo.
pause
