@echo off
echo === Lucky Spin Backend Setup ===
echo.

cd /d D:\lucky-spin

echo [1/5] Creating Laravel project...
composer create-project laravel/laravel backend-laravel --prefer-dist

echo.
echo [2/5] Copying custom files...

REM Copy app files
xcopy /E /Y /I backend\app backend-laravel\app

REM Copy database files
xcopy /E /Y /I backend\database\migrations backend-laravel\database\migrations
xcopy /E /Y /I backend\database\seeders backend-laravel\database\seeders

REM Copy routes
copy /Y backend\routes\api.php backend-laravel\routes\api.php

REM Copy config
copy /Y backend\config\cors.php backend-laravel\config\cors.php

REM Copy bootstrap
copy /Y backend\bootstrap\app.php backend-laravel\bootstrap\app.php

echo.
echo [3/5] Removing old backend folder...
rmdir /S /Q backend

echo.
echo [4/5] Renaming to backend...
rename backend-laravel backend

echo.
echo [5/5] Done!
echo.
echo Next steps:
echo   cd backend
echo   copy .env.example .env
echo   php artisan key:generate
echo   # Edit .env with your database settings
echo   php artisan migrate
echo   php artisan db:seed --class=PrizeSeeder
echo   php artisan serve
echo.
pause
