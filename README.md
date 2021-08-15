<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


1. Get this project from Github (git clone).
2. Copy ".env.example" file and rename to ".env". Edit the .env file (connect to DB).
3. Run "composer install".
4. Run "php artisan key:generate". It will add application key to the .env file.
5. Run "php artisan migrate" Laravel Migrations.
6. Run "php artisan db:seed" Laravel Seeding.
7. Run "php artisan serve".
You can find Login Credentials (email, password) inside UsersTableSeeder class.
Route for Swagger API is /api/documentation.

