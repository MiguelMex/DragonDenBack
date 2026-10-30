# DenBack

Plataforma para escritores y lectores donde los autores pueden redactar sus obras en borradores, publicarlas por capítulos y compartirlas con una comunidad que puede leer, comentar, seguir autores y organizar sus lecturas.

El proyecto expone una **API REST** (autenticada con Laravel Sanctum) y un **panel web** construido con Inertia + React sobre el starter kit oficial de Laravel.

---

## Características

- **Perfiles de usuario** con nombre, apodo, descripción y avatar.
- **Proyectos en progreso (WIPs)** y **borradores** de escritura (contenido almacenado como JSON en disco privado).
- **Obras y capítulos**: los capítulos se publican a partir de un borrador y pueden actualizarse desde él.
- **Portadas** para obras y capítulos.
- **Géneros** (máximo 2 por obra) y **restricciones de edad**, administrables solo por administradores.
- **Colecciones** (del autor) y **listas de lectura** (de obras de otros autores).
- **Comentarios** por capítulo con respuestas anidadas.
- **Historial de lectura** por usuario.
- **Seguidores / seguidos** entre perfiles.
- **Administración de usuarios** (CRUD restringido a administradores).
- **Autenticación web** con Laravel Fortify (registro, login, recuperación de contraseña, verificación de correo y 2FA).
- **Modo claro/oscuro** en la interfaz.

## Stack tecnológico

| Capa | Tecnología |
| --- | --- |
| Backend | PHP ^8.3, Laravel 13 |
| Autenticación | Laravel Fortify (web) + Laravel Sanctum (API por tokens) |
| Frontend | React 19, TypeScript, Inertia.js 3 |
| Estilos / UI | Tailwind CSS 4, shadcn/ui (Radix UI), lucide-react |
| Build | Vite 8, Laravel Wayfinder |
| Base de datos | PostgreSQL (por defecto), SQLite en pruebas |
| Pruebas | Pest 4 |
| Calidad de código | Laravel Pint, ESLint, Prettier |
| Despliegue | Railway |

## Requisitos previos

- PHP 8.3 o superior (extensiones habituales de Laravel, `pdo_pgsql`)
- Composer 2
- Node.js 22 (ver `.node-version`) y npm
- PostgreSQL

## Instalación

```bash
# 1. Clonar el repositorio
git clone <URL_DEL_REPOSITORIO> DenBack
cd DenBack

# 2. Instalar dependencias, crear .env, generar la clave, migrar y compilar
composer setup
```

El script `composer setup` ejecuta: `composer install`, copia `.env.example` a `.env`, `php artisan key:generate`, `php artisan migrate --force`, `npm install` y `npm run build`.

### Configuración del entorno

Edita el archivo `.env` y configura tu conexión a la base de datos. La configuración por defecto de la aplicación usa PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Den
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

Otras variables relevantes:

```env
APP_URL=http://localhost

# Credenciales del administrador creado por el seeder
ADMIN_EMAIL=admin@gmail.com
ADMIN_PASSWORD=cambia_esta_contraseña
```

### Datos iniciales

```bash
php artisan db:seed          # Crea el usuario administrador y su perfil
php artisan storage:link     # Enlace público para portadas, avatares y capítulos
```

Coloca además las imágenes por defecto `covers/default.jpeg` y `avatars/default.jpeg` en `storage/app/public`, ya que la API las asigna a obras, capítulos y perfiles nuevos.

## Desarrollo

```bash
composer dev
```

Levanta en paralelo el servidor de Laravel, la cola, los logs (`pail`) y Vite.

Otros comandos útiles:

```bash
npm run dev          # Solo Vite
npm run build        # Compilar assets
npm run build:ssr    # Compilar con SSR
npm run lint         # ESLint (con --fix)
npm run format       # Prettier
npm run types:check  # Verificar tipos de TypeScript
composer lint        # Laravel Pint
```

> El renderizado del lado del servidor (SSR) de Inertia está habilitado en `config/inertia.php` y apunta a `http://127.0.0.1:13714`. Si no ejecutas el servidor SSR, desactívalo allí (`'enabled' => false`).

## Pruebas

```bash
composer test            # Limpia config, ejecuta Pint y las pruebas
./vendor/bin/pest        # Solo las pruebas
```

Las pruebas usan SQLite en memoria (ver `phpunit.xml`).

## Estructura del proyecto

```
app/
├── Actions/Fortify/        # Acciones de registro y reseteo de contraseña
├── Concerns/               # Reglas de validación reutilizables
├── Http/
│   ├── Controllers/
│   │   ├── Api/            # Controladores de la API REST
│   │   └── Settings/       # Controladores de ajustes (perfil, seguridad, 2FA)
│   ├── Requests/           # Form Requests por módulo
│   └── Resources/          # API Resources (respuestas JSON)
├── Models/                 # Modelos Eloquent (UUID como llave primaria)
└── Providers/
database/
├── migrations/
├── seeders/                # AdminUserSeeder
└── factories/
resources/js/
├── components/             # Componentes y UI (shadcn/ui)
├── layouts/
├── pages/                  # Páginas Inertia (auth, settings, dashboard)
└── hooks/
routes/
├── api.php                 # Rutas de la API
├── web.php                 # Rutas web
└── settings.php            # Rutas de ajustes de cuenta
tests/                      # Pruebas Pest
```

## Modelo de datos

Todas las tablas usan **UUID** como llave primaria.

```
users ──1:1── profiles ──1:N── wips ──1:N── drafts ──1:1── chapters
                  │                                          │
                  ├──1:N── works ──1:N───────────────────────┘
                  │          ├── N:M ── genres        (genre_works)
                  │          ├── N:1 ── age_restrictions
                  │          ├── N:M ── collections   (collections_works)
                  │          └── N:M ── read_lists    (read_lists_works)
                  ├──1:N── collections
                  ├──1:N── read_lists
                  ├──1:N── comments (auto-referenciada por `response`)
                  ├──1:N── read_histories
                  └── N:M ── profiles (following)
```

## API REST

Prefijo base: `/api`. Las rutas marcadas con 🔒 requieren el header `Authorization: Bearer <token>` (Sanctum).

### Autenticación

| Método | Ruta | Descripción |
| --- | --- | --- |
| POST | `/register` | Registrar usuario (devuelve perfil y token) |
| POST | `/login` | Iniciar sesión (devuelve token) |
| POST | `/logout` 🔒 | Cerrar sesión |
| GET | `/user` | Perfil del usuario autenticado |
| PUT | `/profile` 🔒 | Actualizar perfil |
| PUT | `/password` 🔒 | Cambiar contraseña |
| GET | `/admin` 🔒 | Verificar si el usuario es administrador |

### Recursos

| Recurso | Ruta base | Rutas adicionales |
| --- | --- | --- |
| Usuarios (solo admin) 🔒 | `/users` | CRUD |
| Perfiles | `/profiles` | `/{id}/wips`, `/{id}/works`, `/{id}/lists`, `/{id}/collections`, `/{id}/comments`, `/{id}/followers`, `/{id}/following`, `/{id}/histories` 🔒, `/self/*` 🔒, `/profiles/follow` 🔒, `/profiles/unfollow` 🔒 |
| Obras | `/works` | `/latest`, `/{id}/author`, `/{id}/chapters`, `/{id}/chapterCount`, `/{id}/genres`, `/{id}/comments`, `/{id}/collections`, `/{id}/lists`, `/storeCover` 🔒, `/addGenre` 🔒, `/updateGenres` 🔒 |
| Capítulos | `/chapters` | `/{id}/work`, `/{id}/draft`, `/{id}/comments`, `/{id}/histories`, `/content`, `/updateContent`, `/cover` 🔒 |
| WIPs | `/wips` | `/{id}/user`, `/{id}/drafts`, `/{id}/work` |
| Borradores | `/drafts` | `/{id}/wip`, `/{id}/chapter`, `/content`, `/content/store` |
| Géneros | `/genres` | `/{id}/works`, `/addwork` 🔒, `/detachWork` 🔒 |
| Restricciones de edad | `/restrictions` | `/{id}/works` |
| Colecciones | `/collections` | `/{id}/works`, `/{id}/user`, `/addwork` 🔒, `/removeWork` 🔒 |
| Listas de lectura | `/lists` | `/{id}/works`, `/{id}/user`, `/addWork` 🔒, `/removeWork` 🔒 |
| Comentarios | `/comments` | `/{id}/chapter`, `/{id}/user`, `/{id}/parent`, `/{id}/children` |
| Historial de lectura | `/history` | `/{id}/user` 🔒, `/{id}/chapter` 🔒, `/{id}/work` 🔒 |

Para el listado completo y actualizado ejecuta:

```bash
php artisan route:list --path=api
```

### Reglas de negocio destacadas

- Géneros y restricciones de edad solo pueden ser creados, editados o eliminados por **administradores**.
- Una obra puede tener como máximo **2 géneros**.
- Un autor no puede repetir el título de sus propias obras, ni el nombre de sus colecciones, listas, WIPs o borradores.
- Solo el autor (o un administrador) puede editar o eliminar sus obras y capítulos.
- Las **colecciones** solo aceptan obras del propio autor; las **listas de lectura** solo aceptan obras de **otros** autores.
- Los capítulos se crean copiando el contenido de un borrador (`drafts`, disco privado) al disco público (`chapters/`).
- Tamaño máximo de imágenes (portadas y avatares): 2 MB.

## Almacenamiento

| Contenido | Disco | Ruta |
| --- | --- | --- |
| Borradores | `local` (privado) | `storage/app/private/drafts/*.json` |
| Capítulos publicados | `public` | `storage/app/public/chapters/*.json` |
| Portadas | `public` | `storage/app/public/covers/` |
| Avatares | `public` | `storage/app/public/avatars/` |

## Despliegue en Railway

El archivo `railway.json` define el despliegue:

- **Post-deploy:** `php artisan migrate --force && php artisan db:seed --force && php artisan storage:link`
- **Health check:** `GET /health`
- **Reinicio:** `ON_FAILURE`

Configura en Railway las variables de entorno necesarias (`APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, credenciales de base de datos y `ADMIN_EMAIL` / `ADMIN_PASSWORD`).

## Seguridad

- **Nunca** subas credenciales al repositorio. Define `DB_PASSWORD` y el resto de secretos únicamente mediante variables de entorno.
- Cambia `ADMIN_PASSWORD` antes de desplegar en producción.
- Restringe `allowed_origins` en `config/cors.php` a los dominios de tu cliente.

## Licencia

Este proyecto se distribuye bajo la licencia [MIT](https://opensource.org/licenses/MIT).
