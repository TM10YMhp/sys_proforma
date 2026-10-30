@echo off

start "Backend Laravel" cmd /k "cd backend-web && php artisan serve"

start "WebAPI Proforma" cmd /k "cd GeneradorProforma && GeneradorProforma.WebAPI.exe"

start "Tunnel Zrok" cmd /k "zrok2 share public 8000 -n public:sysproforma --headless"

exit
