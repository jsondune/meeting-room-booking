@echo off
REM Quick Fix Script for Backend Routing
REM Save this as: fix-backend-routing.bat
REM Run from: C:\xampp\htdocs\erp-sis\

echo ========================================
echo Backend Routing Quick Fix
echo ========================================
echo.

echo Step 1: Creating .htaccess file...
cd backend\web

(
echo RewriteEngine on
echo.
echo # If a directory or a file exists, use the request directly
echo RewriteCond %%{REQUEST_FILENAME} !-f
echo RewriteCond %%{REQUEST_FILENAME} !-d
echo.
echo # Otherwise forward the request to index.php
echo RewriteRule . index.php
echo.
echo # Prevent directory listing
echo Options -Indexes
echo.
echo # Follow symbolic links
echo Options +FollowSymLinks
echo.
echo # Deny access to .git and other hidden files
echo RedirectMatch 404 /\..*$
) > .htaccess

echo [OK] .htaccess created in backend\web\
echo.

echo Step 2: Clearing cache...
cd ..\..
if exist "backend\runtime\cache" (
    rd /s /q backend\runtime\cache
    echo [OK] Cleared backend\runtime\cache\
)

if exist "backend\web\assets" (
    rd /s /q backend\web\assets
    echo [OK] Cleared backend\web\assets\
)
echo.

echo Step 3: Creating directories...
mkdir backend\runtime\cache 2>nul
mkdir backend\web\assets 2>nul
echo [OK] Directories created
echo.

echo ========================================
echo Fix Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Restart Apache in XAMPP
echo 2. Update backend\config\main.php urlManager
echo 3. Test: http://backend.sis.test/
echo.
pause
