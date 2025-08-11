@echo off
echo Cache temizleniyor...
php artisan config:clear
php artisan cache:clear
php artisan permission:cache-reset

echo.
echo Migration yeniden calistiriliyor...
php artisan migrate:fresh --seed

pause
