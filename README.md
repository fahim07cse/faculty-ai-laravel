# Faculty AI Explorer — Laravel

Laravel conversion of the Faculty AI Explorer project.

## Features

- Faculty Explorer
- Faculty search and network graph
- Faculty submission/editing
- Laravel API endpoints
- Admin login/logout
- Admin dashboard
- Faculty edit
- Soft delete and restore
- Admin user management
- Activity logging
- CSV export
- Excel/CSV import from the admin UI
- MySQL-ready migrations

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ (or compatible database)
- Node.js/npm if you want to build frontend assets

## Installation

```bash
git clone https://github.com/YOUR_USERNAME/faculty-ai-laravel.git
cd faculty-ai-laravel

composer install

cp .env.example .env
php artisan key:generate

php artisan migrate

php artisan serve
```

Configure your database in `.env` before running migrations.

## Create an admin

```bash
php artisan tinker
```

```php
App\Models\AdminUser::create([
    'username' => 'admin',
    'password' => Illuminate\Support\Facades\Hash::make('Nusr@t480317'),
    'role' => 'super_admin',
    'is_active' => true,
]);
```

Then open:

- Faculty Explorer: http://127.0.0.1:8000/
- Admin Portal: http://127.0.0.1:8000/admin

## GitHub

Do not commit `.env` or real credentials.

```bash
git init
git add .
git commit -m "Initial Laravel Faculty AI project"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/faculty-ai-laravel.git
git push -u origin main
```

## Security

Change the example admin password before using the application in production. Never publish database credentials, API keys, or `.env` files.
