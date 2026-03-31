# M3lesh Backend

Laravel REST API plus a Filament admin panel for the M3lesh product. Admins manage content and settings through `/admin`; mobile and web clients consume JSON under `/api`. Authentication for admin routes uses Laravel Sanctum; roles and permissions use Spatie Laravel Permission.

## Requirements

- PHP 8.4+ (see `composer.json`)
- Composer
- MySQL or MariaDB
- Node.js and npm (only if you rebuild Filament/front assets)

## Quick start

```bash
git clone <repo-url> m3lesh-backend && cd m3lesh-backend
composer install
cp .env.example .env
php artisan key:generate
```

Set database and `APP_URL` in `.env`, then:

```bash
php artisan migrate
php artisan db:seed   # optional
php artisan serve
```

- **API:** `http://localhost:8000/api/...` (or your `APP_URL`)
- **Admin (Filament):** `/admin` — site root `/` redirects there

## API at a glance

| Prefix | Use |
|--------|-----|
| `/api/user/*` | Public read-only `GET` — no token |
| `/api/admin/*` | Admin reads + auth (`login`, `me`, `logout`, list/show) — Bearer token |
| `/api/*` | Admin writes (`POST` / `PUT` / `PATCH` / `DELETE`) and some legacy paths — Bearer token |

Obtain a token with `POST /api/admin/login` (`email`, `password`); send `Authorization: Bearer <token>` on protected routes.

**Clients should also send:**

- `Accept: application/json`
- `Accept-Language: en` or `ar` for localized fields where the API exposes `*_en` / `*_ar` (or equivalent)

**List endpoints** commonly support `?per_page=` (capped), `?page=`, and, where documented, `sort_by`, `sort_order`, and filters (`search`, `is_active`, domain-specific ids, etc.).

**Main domains exposed in the API** include admin auth and RBAC, directory (categories, listings), shop (categories, shops, sections, products, cart/checkout), news, ads, marketplace, events, job listings, support tickets, transportation, notifications, and related admin CRUD.

## Postman

Import `postman/m3lesh-apis.postman_collection.json`. Use environment files under `postman/` for `base_url` and language. See `postman/README.md` for variables and login flow (collection scripts set `Accept-Language` and persist `admin_token` after login). There is no separate OpenAPI spec; keep the collection in sync when routes change.

## Development

```bash
./vendor/bin/pint              # code style
./vendor/bin/phpstan analyse   # static analysis (see phpstan.neon)
php artisan test               # automated tests
```

## Deployment notes

**Shared hosting (document root = project root, not `public/`):** The repo includes a root `index.php` that forwards to `public/index.php`. Set `APP_URL` in production with no trailing slash. After deploy, if routes misbehave: `php artisan config:clear`, `php artisan route:clear`, then `config:cache` and `route:cache`.

**GitHub Actions:** On push to `main`, `.github/workflows/deploy.yml` SSHs to the server, pulls with `git`, runs `composer install --no-dev`, clears/caches Laravel config and routes, runs migrations, and adjusts permissions. Add the secrets that workflow expects (e.g. `SSH_HOST`, `SSH_USER`, `SSH_KEY`, `APP_PATH`, `GH_TOKEN`, and optionally `SSH_PORT`).

## License

MIT.
