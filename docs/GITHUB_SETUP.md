# GitHub + Render setup

## 1. Push to GitHub

```bash
git init
git add .
git commit -m "Prepare Faculty AI Laravel app for deployment"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/faculty-ai-laravel.git
git push -u origin main
```

Before pushing, confirm `.env` is ignored:

```bash
git status
git check-ignore .env
```

## 2. Deploy with Render Blueprint

Open Render, choose **New → Blueprint**, connect the GitHub repository and select this repository. Render reads `render.yaml` and creates:

- `faculty-ai-explorer` web service
- `faculty-ai-db` PostgreSQL database

When prompted, provide `ADMIN_USERNAME` and a strong `ADMIN_PASSWORD`.

The Docker container automatically runs:

```bash
php artisan migrate --force
php artisan db:seed --force
apache2-foreground
```

## 3. Verify

- `/` — Faculty Explorer
- `/admin` — Admin Portal
- `/up` — Laravel health check
