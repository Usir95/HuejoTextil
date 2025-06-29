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

## [v1.5.0] - Agregado soporte para idioma español
- Se agregaron archivos de traducción en `resources/lang/es`
- Se configuró Laravel para usar `locale = es`

## [v1.6.0] - 2025-06-27
### Añadido
- Configuración de `.prettierrc` para mantener estilo de código consistente en archivos JavaScript y Vue.
- Configuración de `pint.json` para formateo automático de código PHP.
- Consolidación de estilo de codificación en `.editorconfig`.

## [1.6.1] - 2025-06-27

### Added
- Campo `usuario` en la tabla `usuarios`, junto a renombramiento de `email` a `correo`
- Validación y edición del campo `usuario` desde el formulario de perfil
- Soporte para inicio de sesión configurable vía `fortify.login_field` (`.env`)
- Control de nombres de campo en español (`correo`, `nombre`, `usuario`)
- Personalización de la lógica de autenticación en `FortifyServiceProvider.php`

### Changed
- Formularios de login y registro para utilizar `usuario` en lugar de `email`
- `UpdateProfileInformationForm.vue` para permitir editar los tres campos clave
- Configuración y validaciones actualizadas para evitar colisiones al actualizar `usuario` o `correo`

### Notes
- Compatible con Laravel 12.
- Pensado para facilitar la transición a Laravel 13 sin conflictos.

## [v1.7.0] - 2025-06-28

### Added
- Configuración `MULTIPLE_SESSIONS` en `.env` para permitir o restringir múltiples sesiones por usuario.
- Validación automática en el login para cerrar sesiones anteriores si `MULTIPLE_SESSIONS=false`.

### Changed
- Fortify ahora utiliza la sesión actual y elimina otras sesiones activas si la opción está deshabilitada.

### Security
- Mejora en la protección de sesiones activas por usuario, evitando accesos simultáneos no autorizados.

## [v1.8.0] - 2025-06-28
### Agregado
- Se agregó archivo `config/proyecto.php` para centralizar parámetros personalizados (nombre, logo, soporte, colores).

# Changelog

## [v1.9.0] - 2025-06-28
### Añadido
- Fuentes personalizadas de Google Fonts: Fredoka, Patrick Hand, Caveat.
- Aplicación global de la fuente Caveat a todas las etiquetas h1–h6 con `font-weight: 800`.
- Definición de la fuente --font-sans con fallback personalizado para Tailwind CSS v4.
- Soporte para uso directo de SVGs como código y como importación.

