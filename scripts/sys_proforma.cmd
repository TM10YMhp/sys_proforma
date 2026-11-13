@echo off

set "ROOT_DIR=%~dp0"

where wt.exe >nul 2>nul
if %errorlevel% equ 0 (
  wt.exe -d "%ROOT_DIR%backend-web" cmd /k php artisan serve; ^
  nt -d "%ROOT_DIR%GeneradorProforma" cmd.exe /k GeneradorProforma.WebAPI.exe; ^
  nt cmd.exe /k zrok2 share public 8000 -n public:sysproforma --headless
) else (
  start "Backend Laravel" cmd /k "cd backend-web && php artisan serve"
  start "WebAPI Proforma" cmd /k "cd GeneradorProforma && GeneradorProforma.WebAPI.exe"
  start "Tunnel Zrok" cmd /k "zrok2 share public 8000 -n public:sysproforma --headless"
)

exit
