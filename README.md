<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Chatlyst

Simple CRM for Healthcare. Build with Laravel.

## How to Install

1. Clone
2. Install dependencies

    ```bash
    composer install --optimize-autoloader --no-dev

    npm install

    npm run build
    ```

3. Setup database
   Create database and run migrations

    ```bash
        php artisan migrate --force --seed
    ```

4. Setup environment

    ```bash
    php artisan key:generate

    php artisan migrate --force --seed

    php artisan shield:generate

    php artisan permission:cache-reset

    php artisan reverb:install

    npm install

    npm run build
    ```

5. Sync database

    ```bash
    php artisan sync:education --force

    php artisan sync:jobtitle --force

    // ... sync data pegawai Sango
    php artisan sync:person --force

    php artisan sync:patient --force
    ```

## Notes:

Jika super admin kena Forbidden, jalankan:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Administrator via [dev@sypspace.com](mailto:dev@sypspace.com). All security vulnerabilities will be promptly addressed.
