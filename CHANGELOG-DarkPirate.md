# Registro de cambios - Proyecto Base DarkPirate

Fecha: 2025-06-28

## Versión: v1.0.0

### 🔧 Configuraciones iniciales

- Laravel 12 con Inertia.js + Vue 3 + Tailwind CSS.
- Actualización completa a Tailwind CSS v4 usando `npx @tailwindcss/upgrade`.
- Commit asociado: Actualización a Tailwind 4.
- Etiquetas y ramas de Git configuradas para control de versiones.

### Added

- Comando `make:module` para generar modelo, migración, seeder, controlador Inertia y vista Vue con plantilla estructurada.
- Comando `make:view` para crear una vista Inertia con estructura base Vue, sin modelo asociado.

### 🌐 Localización

- Laravel configurado en idioma español (`lang/es` publicado y activo).
- Commit: Configuración de idioma base.

### ⚙️ Personalización de Stubs

#### `model.stub`

- Uso de `SoftDeletes` y `OwenIt\Auditing` en modelos por defecto.
- Ocultamiento automático de estos campos (`$hidden`).
- Configuración de constantes `CREATED_AT`, `UPDATED_AT`, `DELETED_AT`.

### ✅ Buenas prácticas

- Control de versiones claro: commits atómicos, ramas por feature, uso de etiquetas Git.
- Proyecto listo para migración futura a Laravel 13.
