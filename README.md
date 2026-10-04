# Faculty AI Explorer — Laravel

Laravel version of the Faculty AI Explorer project.

## Included

- Faculty Explorer and search
- Network graph and matching logic
- Faculty submission/editing
- Admin login/logout
- Admin dashboard
- Faculty edit, soft-delete and restore
- Admin user management
- Activity logging
- CSV export
- Excel/CSV import in the admin UI
- PostgreSQL-ready migrations
- Render Docker deployment configuration

## Requirements

- PHP 8.3+
- Composer 2+
- PostgreSQL 14+ or MySQL 8+ for local development
- Docker Desktop if you want to test the production container locally

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Set `ADMIN_USERNAME` and `ADMIN_PASSWORD` in `.env` before running `php artisan db:seed` if you want the initial admin created automatically.

## GitHub

Do not commit `.env`, passwords, database URLs, or API keys.

```bash
git init
git add .
git commit -m "Prepare Faculty AI Laravel app for deployment"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/faculty-ai-laravel.git
git push -u origin main
```

## Render deployment

This repository includes `Dockerfile` and `render.yaml` for Render.

1. Push the repository to GitHub.
2. In Render, create a new Blueprint and select the GitHub repository.
3. Render will create a free web service and a free PostgreSQL database from `render.yaml`.
4. Enter values for `ADMIN_USERNAME` and `ADMIN_PASSWORD` when Render asks for the secret values.
5. Deploy and open the generated `.onrender.com` URL.
6. Admin portal: `/admin`.

### Important free-tier limitation

Render's free web service can sleep after 15 minutes without traffic, and a free Render Postgres database expires after 30 days. Free instances are intended for testing/hobby use rather than production. Upgrade the database before using this as a permanent production system.

## Security

- `APP_DEBUG=false` in production.
- Never commit `.env`.
- Use a strong unique admin password.
- The initial seeder never overwrites an existing admin password.
