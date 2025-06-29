<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# DarkPirate ⚓️

**DarkPirate** es un proyecto base reutilizable construido sobre Laravel 12 + Inertia + Vue 4 + Tailwind.
Está diseñado para servir como plantilla inicial para sistemas administrativos y ERP, permitiendo clonar, escalar y extender con mínimos cambios.

## Características
- Tipografías personalizadas (Fredoka, Patrick Hand, Caveat)
- Soporte para SVGs dinámicos en vistas
- Compatible con Laravel 12 y Tailwind v4 sin `tailwind.config.js`
- Layout inicial listo para personalización

---

## 🚀 Stack Tecnológico

- Laravel 12
- Jetstream (Inertia + Vue 4)
- Tailwind CSS (con modo oscuro)
- Laravel Sanctum (API)
- Verificación de email
- Preparado para PostgreSQL o MySQL

---

## 🧰 Instalación

git clone https://github.com/Usir95/DarkPirate.git
cd DarkPirate
cp .env.example .env
npx @tailwindcss/upgrade
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate

## 🛠 Comandos personalizados Artisan

### `php artisan make:module Nombre`

Crea:

- Modelo con migración y seeder
- Controlador tipo resource
- Vista Inertia en `resources/js/Pages/Nombre/Nombre.vue` con plantilla base

### `php artisan make:view Nombre`

Crea:

- Carpeta y archivo Inertia `resources/js/Pages/Nombre/Nombre.vue` con estructura base, sin controlador ni modelo


## Estilo de Código

Este proyecto mantiene un estilo de codificación consistente para todos los colaboradores mediante las siguientes herramientas:

- **EditorConfig** (`.editorconfig`): Define reglas generales para todos los archivos (indentación, saltos de línea, etc.).
- **Prettier** (`.prettierrc`): Formato para archivos JavaScript y Vue.
- **Laravel Pint** (`pint.json`): Formateador de código PHP basado en reglas del ecosistema Laravel.

Ejecutar Pint manualmente: composer lint

### Autenticación

Este proyecto usa Laravel Fortify y permite configurar el campo de inicio de sesión mediante `.env`:

### .env

```env
LOGIN_FIELD=usuario

## Configuración de sesiones múltiples

El sistema permite controlar si un usuario puede tener varias sesiones abiertas al mismo tiempo.

### .env

```env
MULTIPLE_SESSIONS=false

#APP_NOMBRE=
#APP_LOGO=
#APP_SOPORTE_EMAIL=

# Proyecto Base Laravel + Inertia + Tailwind

## Monitoreo con Laravel Pulse

Este proyecto incluye Laravel Pulse como herramienta de monitoreo en tiempo real de rendimiento. Para usarlo:

1. Asegúrate de tener una base de datos compatible (PostgreSQL recomendado).
2. Si la tabla `pulse_entries` no existe, ejecuta:
   ```bash
   php artisan vendor:publish --tag=pulse-migrations
   php artisan migrate

## Laravel spatie / laravel-permission
    1. composer require spatie/laravel-permission
    2. php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"


