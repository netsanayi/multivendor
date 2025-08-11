@echo off
cls
echo ===============================================
echo Laravel MV - User ve Role Model Sorunu Cozumu
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
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [ADIM 3] Migration calistiriliyor (name alani ekleniyor)...
php artisan migrate

echo.
echo [ADIM 4] Roller ve izinler olusturuluyor...
php artisan db:seed --class=RoleSeeder

echo.
echo [ADIM 5] Model kontrolleri yapiliyor...
php artisan tinker --execute="echo 'User Model: ' . (class_exists('App\Models\User') ? 'OK' : 'HATA'); echo PHP_EOL; echo 'Role Model: ' . (class_exists('App\Modules\Roles\Models\Role') ? 'OK' : 'HATA');"

echo.
echo ===============================================
echo COZUM TAMAMLANDI!
echo ===============================================
echo.
echo User Model: App\Models\User
echo Role Model: App\Modules\Roles\Models\Role
echo.
echo Simdi projeniz calismaya hazir!
echo.
pause
