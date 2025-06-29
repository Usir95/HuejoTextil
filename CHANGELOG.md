# 📓 Registro de cambios - Proyecto Base DarkPirate

📅 **Fecha de inicio:** 2025-06-27  
🚀 **Stack:** Laravel 12 + Inertia.js + Vue 3 + Tailwind CSS 4  
📂 **Convención de versiones:** SemVer (`vX.Y.Z`)

---

## ✅ [v1.0.0] - Proyecto base inicial

### 🔧 Configuración
- Laravel Jetstream con Inertia.js y Vue 3.
- Integración con Tailwind CSS 4 usando `@tailwindcss/upgrade`.
- Proyecto base listo para clonado y reutilización.

### ✨ Funcionalidades agregadas
- Comando `make:modulo` (modelo, migración, factory, seeder, controlador e Inertia view).
- Comando `make:vista` para generar vistas Inertia básicas.

### 🌐 Localización
- Laravel configurado en español.
- Archivos `lang/es` publicados.

### ⚙️ Personalización de stubs
- `model.stub`: incluye `SoftDeletes`, `Auditable`, timestamps, ocultamiento de campos y constantes de fechas.

---

## 🧩 [v1.5.0] - Soporte completo para idioma español

- Archivos `lang/es` cargados.
- Laravel configurado con `locale = es`.

---

## 💅 [v1.6.0] - Estilo de código y buenas prácticas

### ➕ Añadido
- `.prettierrc` para JavaScript y Vue.
- `pint.json` para formateo de código PHP.
- `.editorconfig` para estilo uniforme en todo el proyecto.

---

## 👤 [v1.6.1] - Personalización de login y perfil

### ➕ Added
- Campo `usuario` en lugar de `email`.
- Edición de `nombre`, `correo`, `usuario` desde perfil.
- Login configurable vía `.env` con `fortify.login_field`.

### 🔁 Changed
- Vistas de login y registro adaptadas a nuevos campos.
- Validaciones actualizadas en backend.

---

## 🔐 [v1.7.0] - Control de sesiones

- Agregado `.env/MULTIPLE_SESSIONS`.
- Cierre de sesiones anteriores si está desactivado.
- Protección contra múltiples accesos simultáneos.

---

## 🎨 [v1.8.0] - Parámetros del sistema centralizados

- Agregado archivo `config/proyecto.php`: nombre, logo, soporte, colores.

---

## 🔤 [v1.9.0] - Tipografía y estilo visual

- Fuentes: Fredoka, Patrick Hand, Caveat.
- Estilo h1–h6 con `font-weight: 800`.
- Fuente personalizada para `--font-sans` y soporte de SVG directo.

---

## 🔑 [v1.10.0] - Reglas de contraseña configurables

- Nuevo archivo `config/password_rules.php`.
- Soporte `.env` para personalizar: longitud, símbolos, números, etc.

---

## 📊 [v1.12.0] - Monitoreo en tiempo real

- Instalación de Laravel Pulse.
- Migración `pulse_entries` aplicada.
- Dashboard habilitado.

---

## 🔐 [v1.13.0] - Roles y permisos

- Integración del paquete `spatie/laravel-permission`.
- Listo para configuración personalizada por módulo o rol.

---

## 📄 [v1.14.0] - Librerías comunes

### Excel
- Paquete: `maatwebsite/excel`
- Instalado y publicado.

### PDF
- Paquete: `barryvdh/laravel-dompdf`
- Instalado y publicado.

---

## 🕵️ [v1.15.0] - Auditoría de modelos

### ➕ Added
- Instalación de `owen-it/laravel-auditing`.
- Migración `create_audits_table` ejecutada.
- Configuración `audit.php` publicada.

### 📌 Uso
```php
use OwenIt\Auditing\Contracts\Auditable;

class MiModelo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
