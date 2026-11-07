@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo ============================================
echo   Lost and Found System - MVP Setup
echo ============================================
where php >nul 2>nul
if errorlevel 1 (
  echo ERROR: PHP is not installed or not added to PATH.
  echo Install PHP 8.2+ or use the PHP included with XAMPP.
  pause
  exit /b 1
)
where composer >nul 2>nul
if errorlevel 1 (
  echo ERROR: Composer is not installed or not added to PATH.
  echo Install Composer, then run this file again.
  pause
  exit /b 1
)
if not exist .env copy .env.example .env >nul
if not exist database\database.sqlite type nul > database\database.sqlite
if not exist vendor\autoload.php (
  echo Installing PHP dependencies...
  composer install
  if errorlevel 1 goto failed
)
echo Generating application key if needed...
findstr /R /C:"^APP_KEY=." .env >nul
if errorlevel 1 php artisan key:generate
if errorlevel 1 goto failed
echo Running database migrations and creating demo admin...
php artisan migrate --seed
if errorlevel 1 goto failed
echo.
echo Setup complete. The website will start at http://127.0.0.1:8000
php artisan serve
exit /b 0
:failed
echo.
echo Setup failed. Read the error above and check PHP, Composer, and database settings.
pause
exit /b 1
