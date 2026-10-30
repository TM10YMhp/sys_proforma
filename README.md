# sys_proforma

Sistema de generación y gestión de proformas, compuesto por dos aplicaciones:

| Componente          | Stack                                                | Puerto |
| ------------------- | ---------------------------------------------------- | ------ |
| `backend-web`       | Laravel 13 + Inertia (Svelte), SQLite                | 8000   |
| `GeneradorProforma` | .NET 10 Web API (generación de documentos Excel/PDF) | 5000   |

## Requisitos

Para el uso del release:

- **Windows** (la Web API se publica solo para `win-x64`)
- PHP **8.3+** con las extensiones: `curl`, `gd`, `mbstring`, `pdo_sqlite`, `zip`
  > Las extensiones incluidas se activan descomentando `extension=curl`, `extension=gd`, `extension=mbstring`, `extension=pdo_sqlite`, `extension=zip` en `php.ini`.
- **ASP.NET Core Runtime 10.0**, descargable desde [dotnet.microsoft.com/download/dotnet/10.0](https://dotnet.microsoft.com/download/dotnet/10.0)

Opcionales:

- **zrok** (`zrok2` en PATH) para exponer el sistema por internet

## Instalación por Release (recomendada)

1. Descarga `sys_proforma.zip` desde la sección de [Releases](https://github.com/TM10YMhp/sys_proforma/releases).
2. Extrae el ZIP en una carpeta, por ejemplo `sys_proforma`.
3. Ejecuta `sys_proforma.cmd` (doble clic). Esto abre tres terminales:
   - Backend Laravel → `http://localhost:8000`
   - Web API del generador → `http://localhost:5000`
   - Túnel zrok (opcional) → `https://sysproforma.shares.zrok.io`
4. Abre `http://localhost:8000` en tu navegador.

> El ZIP ya incluye `vendor/` de Composer, los assets del frontend compilados y la base de datos creada. No requiere `composer` ni `npm`.

## Instalación manual (desde el código fuente)

> Requisitos adicionales: **Just**, **Git**, **Composer**, **Node.js 20+** y **.NET SDK 10**.

```bat
git clone git@github.com:TM10YMhp/sys_proforma.git
cd sys_proforma
just build
```

Esto genera en `output/` una carpeta lista igual al release (incluye `sys_proforma.cmd`).

## Exponer por internet (zrok, opcional)

Para compartir el sistema sin instalarlo en cada equipo, usa zrok:

```bat
zrok2 share public 8000 -n public:sysproforma --headless
```

`sys_proforma.cmd` del release ya lo hace automáticamente (asegúrate de tener `zrok2` en el PATH).

Si usas el túnel, ajusta `APP_URL` en `backend-web/.env` a la URL pública generada. Si usas el release no es necesario.

## Variables de entorno clave (`backend-web/.env`)

| Variable                 | Descripción                                          |
| ------------------------ | ---------------------------------------------------- |
| `GENERADOR_PROFORMA_URL` | URL donde corre la Web API (`http://localhost:5000`) |
| `APP_URL`                | URL pública del sistema (ej. la del túnel zrok)      |
