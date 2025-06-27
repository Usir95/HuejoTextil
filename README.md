<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# DarkPirate ⚓️

**DarkPirate** es un proyecto base reutilizable construido sobre Laravel 12 + Inertia + Vue 3 + Tailwind.  
Está diseñado para servir como plantilla inicial para sistemas administrativos y ERP, permitiendo clonar, escalar y extender con mínimos cambios.

---

## 🚀 Stack Tecnológico

- Laravel 12
- Jetstream (Inertia + Vue)
- Tailwind CSS (con modo oscuro)
- Laravel Sanctum (API)
- Verificación de email
- Preparado para PostgreSQL o MySQL

---

## 🧰 Instalación

```bash
git clone https://github.com/Usir95/DarkPirate.git
cd DarkPirate
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate
