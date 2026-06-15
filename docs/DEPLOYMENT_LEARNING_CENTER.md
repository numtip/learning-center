# Learning Center — Docker Deployment Guide

Baseline hardening for local Docker runtime and production handoff. This document covers **first-run**, **database import**, **migrations**, **rollback**, and **host reverse-proxy assumptions** for subpath `/learning-center`.

> **Scope:** Local Docker stack only. Do not treat this as production deploy instructions for the host reverse proxy — that remains a separate, operator-managed step.

---

## Architecture

| Service     | Role                                      | Host exposure        |
|------------|-------------------------------------------|----------------------|
| `web`      | Nginx → static + PHP via FPM              | `127.0.0.1:8086`     |
| `app`      | PHP 8.2-FPM, Laravel runtime              | internal only        |
| `db`       | MariaDB 10.11                             | internal only        |
| `scheduler`| `php artisan schedule:run` every 60s      | internal only        |

Internal Nginx serves Laravel at **document root `/`**. Production URL `https://raeservice.mju.ac.th/learning-center` is handled by the **host** reverse proxy (not modified by this repo).

---

## Prerequisites

- Docker Engine + Docker Compose v2
- Git clone of this repository
- **SQL dump** from the legacy `MJU_Project` database (schema + data). Migrations alone are **not** sufficient — most `mju_*` tables are not created by Laravel migrations in this repo.

---

## First-run steps (local Docker)

### 1. Prepare environment files

```bash
cp .env.docker.example .env.docker
cp .env.production.example .env.production
```

Edit `.env.production` (gitignored — never commit):

| Variable   | Local Docker value                          |
|-----------|---------------------------------------------|
| `APP_KEY` | *(empty until step 4)*                      |
| `APP_URL` | `http://127.0.0.1:8086` for local test    |
| `DB_HOST` | `db`                                        |
| `DB_PORT` | `3306`                                      |
| `DB_DATABASE` | `learning_center` *(match `.env.docker`)* |
| `DB_USERNAME` | `learning_center`                         |
| `DB_PASSWORD` | same as `MYSQL_PASSWORD` in `.env.docker` |

Edit `.env.docker` if you change database credentials from the example defaults.

### 2. Validate Compose configuration

```bash
docker compose --env-file .env.docker config
```

Must exit 0 with no errors.

### 3. Build and start

```bash
docker compose --env-file .env.docker up -d --build
```

On first start:

- Image build runs `composer install`, `npm ci`, `npm run build`.
- Named volume `learning-center-app-html` is seeded from the image (includes `vendor/` and `public/build/`).
- Entrypoint creates storage dirs, runs `php artisan storage:link`, and warms config/route/view caches **only when `APP_KEY` is set**.

### 4. Generate APP_KEY

```bash
docker compose exec app php artisan key:generate --force
```

Re-run cache warmup (or restart app):

```bash
docker compose restart app scheduler
```

Verify:

```bash
docker compose exec app php artisan about
```

### 5. Import database dump

Import **before or instead of** relying on migrations for base schema:

```bash
docker compose exec -T db mariadb -u learning_center -p"${MYSQL_PASSWORD}" learning_center < /path/to/mju_project_dump.sql
```

Or from host:

```bash
docker compose exec -T db mariadb -u learning_center -plearning_center_local_dev learning_center < /path/to/mju_project_dump.sql
```

*(Use the password from your `.env.docker`.)*

### 6. Run migrations (incremental only)

After dump import, apply Laravel migrations that may not exist in the legacy dump:

```bash
docker compose exec app php artisan migrate --force
```

### 7. Container health (before app QA)

```bash
docker compose --env-file .env.docker ps
```

All services should reach `healthy` (or `running` for `scheduler`):

| Service | Health probe |
|---------|----------------|
| `db` | MariaDB `healthcheck.sh --connect` |
| `app` | PHP-FPM port 9000 + `artisan` present |
| `web` | `GET /healthz` → `ok` (Nginx only; no DB required) |
| `scheduler` | no healthcheck — verify with `docker compose logs scheduler` |

```bash
curl -s http://127.0.0.1:8086/healthz
```

Expect: `ok`

### 8. Runtime QA checklist

> **Prerequisite:** SQL dump imported, `APP_KEY` set, migrations applied.  
> Without a dump, expect failures on `/`, `/admin`, and `/superadmin/dashboard` (missing `mju_*` tables).

Run after stack is up:

```bash
BASE=http://127.0.0.1:8086

# Infrastructure (no DB content required)
curl -sS -o /dev/null -w '/healthz → %{http_code}\n' "$BASE/healthz"

# Application routes (require SQL dump + APP_KEY)
curl -sS -o /dev/null -w '/ → %{http_code}\n' "$BASE/"
curl -sS -o /dev/null -w '/login → %{http_code}\n' "$BASE/login"
curl -sS -o /dev/null -w '/admin/dashboard → %{http_code}\n' "$BASE/admin/dashboard"
curl -sS -o /dev/null -w '/superadmin/dashboard → %{http_code}\n' "$BASE/superadmin/dashboard"

# Vite build assets (replace with actual manifest filename if needed)
curl -sS -o /dev/null -w '/build/manifest.json → %{http_code}\n' "$BASE/build/manifest.json"

# Public storage symlink (uploads); 404 acceptable if no files yet, 403/500 indicates link failure
curl -sS -o /dev/null -w '/storage → %{http_code}\n' "$BASE/storage"
```

| Route | Expected (with dump + key) | Without SQL dump |
|-------|---------------------------|------------------|
| `/healthz` | `200` + body `ok` | `200` |
| `/` | `200` or `302` | often `500` |
| `/login` | `200` | often `500` |
| `/admin/dashboard` | `302` → login (unauthenticated) | `403` or `500` |
| `/superadmin/dashboard` | `302` → login (unauthenticated) | `403` or `500` |
| `/build/manifest.json` | `200` | `200` *(from image volume)* |
| `/storage` | `403` or `404` *(not 500)* | same |

Manual browser checks (after creating or importing an admin user):

- [ ] Home page renders slides/news
- [ ] Login form loads and authenticates
- [ ] Admin dashboard loads for `admin` role
- [ ] SuperAdmin dashboard loads for `superadmin` role
- [ ] CSS/JS load from `/build/assets/*`
- [ ] Uploaded image URL under `/storage/*` resolves

Check logs on failure:

```bash
docker compose --env-file .env.docker logs -f app web
```

---

## APP_KEY generation

- **Required** before sessions, encryption, and config cache work correctly.
- Generate once per environment: `php artisan key:generate --force`
- Store only in `.env.production` (gitignored).
- After generating, restart `app` and `scheduler` so entrypoint re-runs `config:cache`.

Never commit `APP_KEY` or copy it into `.env.production.example`.

---

## DB dump import requirement

This application references many tables (`mju_pr_news`, `mju_shop_products`, `mju_orders`, center detail tables, etc.) that are **not** created by the 10 migration files in `database/migrations/`.

| Approach              | When to use                                      |
|-----------------------|--------------------------------------------------|
| **SQL dump import**   | Required for any realistic local or prod cutover |
| **migrate only**      | Insufficient — only adds incremental tables      |

Obtain a current dump from the legacy deployment or DBA before first meaningful test.

**Reminder:** Do not commit dump files to Git. Store dumps outside the repo (e.g. operator secure storage). This repo contains **no** bundled SQL dump — import is a manual gate.

### DB backup (before import, migrations, or upgrades)

Create a snapshot of the current Docker MariaDB volume:

```bash
# Logical backup (recommended)
docker compose --env-file .env.docker exec -T db \
  mariadb-dump -u learning_center -p"${MYSQL_PASSWORD}" \
  --single-transaction --routines --triggers learning_center \
  > "learning_center_backup_$(date +%Y%m%d_%H%M%S).sql"
```

Or using root inside the container:

```bash
docker compose --env-file .env.docker exec -T db \
  mariadb-dump -u root -p"${MYSQL_ROOT_PASSWORD}" \
  --single-transaction learning_center \
  > "learning_center_backup_$(date +%Y%m%d_%H%M%S).sql"
```

Load env vars for substitution:

```bash
set -a && source .env.docker && set +a
```

Restore from backup:

```bash
docker compose --env-file .env.docker exec -T db \
  mariadb -u learning_center -p"${MYSQL_PASSWORD}" learning_center < learning_center_backup_YYYYMMDD_HHMMSS.sql
```

---

## Migration order

Laravel runs migrations by filename timestamp. Current set (in order):

1. `2014_10_12_000000_create_users_table`
2. `2014_10_12_100000_create_password_reset_tokens_table`
3. `2019_08_19_000000_create_failed_jobs_table`
4. `2019_12_14_000001_create_personal_access_tokens_table`
5. `2026_02_28_161520_create_notifications_table`
6. `2026_03_03_113439_add_visit_date_to_center_bookings_table` *(alter — requires `center_bookings` from dump)*
7. `2026_03_03_115851_create_mju_calendar_blocks_table`
8. `2026_03_03_131720_add_reason_to_mju_calendar_blocks`
9. `2026_03_06_071855_add_type_to_center_bookings_table` *(alter — requires `center_bookings` from dump)*
10. `2026_03_16_061752_create_promotions_table`

**Recommended order:** import dump → `php artisan migrate --force`.

If a migration fails because a column already exists in the dump, resolve manually (mark migration run or adjust dump) — document the conflict before production.

---

## Scheduler strategy

Laravel scheduled task (from `app/Console/Kernel.php`):

- `news:move-expired` — daily at 00:05

The `scheduler` Compose service runs:

```text
php artisan schedule:run --verbose --no-interaction
```

every 60 seconds. This matches Laravel’s recommended Docker pattern (equivalent to a cron `* * * * *` entry).

**Production alternatives (operator choice):**

- Keep the Compose `scheduler` service on the app host, or
- Host crontab: `* * * * * docker compose exec -T app php artisan schedule:run`, or
- Kubernetes CronJob / systemd timer.

Only one scheduler runner should be active per environment.

---

## Subpath `/learning-center` — Nginx / proxy assumptions

The **internal** stack Nginx (`docker/nginx/default.conf`) serves the app at `/`. It does **not** configure `/learning-center` prefix routing.

Production URL is configured as:

```text
APP_URL=https://raeservice.mju.ac.th/learning-center
```

### Reverse proxy warning: Docker Nginx vs host Nginx

This stack already includes a **Docker `web` service (Nginx)** bound to `127.0.0.1:8086`. The host reverse proxy must **forward to that upstream**, not replace it with a native/host Nginx document root pointing at PHP files on disk.

| Layer | Role |
|-------|------|
| Host reverse proxy | TLS, `/learning-center` prefix, `X-Forwarded-*` headers |
| Docker `web` (Nginx) | Static files, `public/build`, FastCGI to `app:9000` |
| Docker `app` (PHP-FPM) | Laravel runtime |

**Do not** configure host Nginx with `root /home/.../learning-center/public` unless you intentionally bypass Docker — that bypasses the hardened Compose stack, volume layout, and entrypoint (`storage:link`, cache warmup).

**Do** configure host proxy as HTTP reverse proxy → `http://127.0.0.1:8086/` (prefix strip). This repo does **not** modify host Nginx.

### Required host reverse-proxy behavior (operator-managed)

**Recommended: strip prefix before forwarding** (internal app stays at `/`):

```nginx
location /learning-center/ {
    proxy_pass http://127.0.0.1:8086/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Prefix /learning-center;
}
```

Assumptions:

| Item | Expectation |
|------|-------------|
| Upstream | `127.0.0.1:8086` (Compose `web` binding) |
| Prefix | Host strips `/learning-center` → app sees `/` |
| `APP_URL` | Full public URL **with** subpath |
| `TrustProxies` | App trusts `X-Forwarded-*` headers (`app/Http/Middleware/TrustProxies.php`) |
| Assets | Vite build uses absolute paths from `APP_URL`; verify `/build/assets/*` loads via proxy |
| Uploads | `public/storage` symlink created at container start |

**Not supported in this baseline:** mounting the app under `/learning-center` inside the container Nginx without prefix stripping — would require additional Laravel `URL::forceRootUrl` / asset URL changes.

### Local test vs production URL

For local Docker smoke test, set `APP_URL=http://127.0.0.1:8086` (no subpath). Switch to the production URL only when the host proxy is configured.

---

## Production reverse proxy checklist

Use before go-live (host operator — **not** automated by this repo):

- [ ] TLS certificate valid for `raeservice.mju.ac.th`
- [ ] Proxy passes `/learning-center/` → `127.0.0.1:8086/` with prefix strip
- [ ] `X-Forwarded-Proto`, `X-Forwarded-For`, `Host` headers set
- [ ] `APP_URL` matches public URL including subpath
- [ ] `APP_DEBUG=false`, `APP_KEY` set
- [ ] Database credentials rotated from `.env.docker.example` defaults
- [ ] SQL dump imported and migrations applied
- [ ] `storage:link` verified (`public/storage` → `storage/app/public`)
- [ ] Scheduler running (Compose service or host cron)
- [ ] File upload size ≥ 64M (matches Nginx `client_max_body_size`)
- [ ] Smoke test: home, login, admin, static assets, uploaded images

---

## Rollback

### Stop stack (keep data)

```bash
docker compose --env-file .env.docker down
```

### Stop and remove volumes (destructive)

```bash
docker compose --env-file .env.docker down -v
```

Removes `learning-center-db-data`, `learning-center-storage`, and `learning-center-app-html`.

### Revert to previous image

```bash
docker compose --env-file .env.docker down
git checkout <previous-commit>
docker compose --env-file .env.docker up -d --build
```

Restore database from dump if schema/data changed.

### Migration rollback

```bash
docker compose exec app php artisan migrate:rollback --step=1
```

Only rolls back migrations tracked in `migrations` table — does not restore dropped legacy tables. Prefer DB snapshot restore for major rollback.

---

## Rebuild after code changes

Named volume `learning-center-app-html` persists old `vendor/` and `public/build/` until refreshed:

```bash
docker compose --env-file .env.docker down
docker volume rm learning-center_app-html 2>/dev/null || docker volume rm learning-center-app-html 2>/dev/null || true
docker compose --env-file .env.docker up -d --build
```

*(Volume name may be prefixed with `COMPOSE_PROJECT_NAME`.)*

---

## Files safe to commit

| File | Commit? |
|------|---------|
| `Dockerfile`, `docker-compose.yml`, `docker/` | Yes |
| `.dockerignore`, `.env.docker.example`, `.env.production.example` | Yes |
| `docs/DEPLOYMENT_LEARNING_CENTER.md` | Yes |
| `.env.production`, `.env.docker` | **Never** |

---

## Troubleshooting

| Symptom | Likely cause |
|---------|----------------|
| 502 from Nginx | `app` not ready; check `docker compose logs app` |
| Blank page / 500 | Missing `APP_KEY` or DB connection |
| CSS/JS 404 | Stale `learning-center-app-html` volume — rebuild (see above) |
| Missing tables | Dump not imported |
| Uploads 404 | `storage:link` failed — check entrypoint logs |
| Scheduler not running | `scheduler` container down or `APP_KEY`/DB misconfigured |

---

## Release decision matrix

| Gate | Docker baseline commit | Local runtime QA | Production deploy |
|------|------------------------|------------------|-------------------|
| Compose config valid | Required | Required | Required |
| Healthchecks (`db`, `app`, `web`) | Required | Required | Required |
| `APP_KEY` generated | — | Required | Required |
| SQL dump imported | — | Required | Required |
| Runtime QA checklist pass | — | Required | Required |
| Host reverse proxy configured | — | — | Required |
| Credentials rotated from examples | — | Recommended | Required |

### Current expected decision

- **GO** — Docker baseline files safe to commit (no secrets in diff).
- **NO GO** — production deploy until SQL dump is obtained, imported, and runtime QA checklist passes.
- **NO GO** — meaningful runtime QA until SQL dump is available (container health via `/healthz` only).
