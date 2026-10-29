@echo off

start "Backend Laravel" cmd /k "cd backend-web && php artisan serve"

start "WebAPI Proforma" cmd /k "cd GeneradorProforma && GeneradorProforma.WebAPI.exe"

exit