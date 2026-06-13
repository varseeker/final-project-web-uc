# POS Warkop Kayu — reset DB + migration seed + factory seeder
# Usage:
#   .\scripts\seed-database.ps1          # migrate:fresh --seed (default)
#   .\scripts\seed-database.ps1 -SeedOnly # db:seed saja (tanpa reset tabel)

param(
    [switch]$SeedOnly
)

$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

if ($SeedOnly) {
    Write-Host "Running db:seed..." -ForegroundColor Cyan
    php artisan db:seed --force
} else {
    Write-Host "Running migrate:fresh --seed..." -ForegroundColor Cyan
    php artisan migrate:fresh --seed --force
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "Seed failed (exit $LASTEXITCODE)." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host "Done. Login POS: akun staff dari Inventory (contoh letoy@warkopkayu.test / password)" -ForegroundColor Green
