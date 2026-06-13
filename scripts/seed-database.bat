@echo off
setlocal
cd /d "%~dp0.."

if /i "%~1"=="--seed-only" (
    echo Running db:seed...
    php artisan db:seed --force
) else (
    echo Running migrate:fresh --seed...
    php artisan migrate:fresh --seed --force
)

if errorlevel 1 (
    echo Seed failed.
    exit /b 1
)

echo Done. Admin ^(migration^): test@test / 123 ^| Factory users: password
exit /b 0
