# PulsePress Laravel Blog

PulsePress is a Laravel 12 blog/CMS with:

- Reader, author, and admin accounts
- Multi-niche posts
- Author post submission
- Admin post approval/rejection
- Comment moderation
- User role management
- Modern Blade frontend and backend UI

## Run Without Docker

Go into the Laravel project directory:

```bash
cd src
```

Install PHP dependencies:

```bash
composer install
```

Create a MySQL database:

```sql
CREATE DATABASE laravel_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update `src/.env` with your MySQL username and password:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_blog
DB_USERNAME=root
DB_PASSWORD=your_password
```

Run migrations and seed sample data:

```bash
php artisan migrate:fresh --seed
```

Start Laravel:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Seed Accounts

```text
Admin:  admin@blog.com / password
Author: author@blog.com / password
```

## Notes

This environment has `pdo_mysql` enabled but not `pdo_sqlite`, so the project is configured for MySQL by default.
