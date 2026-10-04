# Deployment-ready checklist

This package was reconstructed as a complete Laravel application around the existing Faculty AI Explorer code.

## Added

- Laravel Composer manifest (`composer.json`)
- Laravel `artisan` entry point
- Laravel bootstrap and application provider registration
- Public front controller (`public/index.php`)
- Production configuration files
- PostgreSQL configuration for Render
- Database seeder for the first admin account
- Dockerfile using PHP 8.4 + Apache
- Render Blueprint (`render.yaml`)
- Runtime storage directories

## Converted

The Faculty Explorer JavaScript no longer calls Supabase. Faculty loading, email lookup, creation, and editing now use the Laravel API routes.

## Before first Render deploy

Render will generate `APP_KEY` automatically. The Docker startup command normalises that generated value into Laravel's `base64:` key format.

Provide `ADMIN_USERNAME` and `ADMIN_PASSWORD` as Render secret environment variables. The seeder creates the admin only if that username does not already exist, so subsequent restarts do not overwrite its password.

## Important

Render's free Postgres database currently expires after 30 days. This package is therefore suitable for a free test/demo deployment, not permanent production storage, until the database plan is upgraded or data is moved elsewhere.
