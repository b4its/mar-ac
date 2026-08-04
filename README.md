<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects.
## clear configurations and optimize css
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize:clear
php artisan optimize
```
## extra clear configuration and optimize css in docker profile nginx
```bash
docker exec -it marac-php-fpm bash -c "php artisan config:clear && php artisan view:clear && php artisan cache:clear && php artisan route:clear && php artisan optimize:clear && php artisan optimize"
```

### menyatukan semua permission pakai perm
```bash
make perm
```

## Docker Setup

### untuk menjalankan nginx dan db
```bash
docker compose --profile nginx up -d
```
### untuk menjalankan apache dan db
```bash
docker compose --profile apache up -d
```

### untuk menjalankan mysql
```bash
docker exec -it marac-db mysql -u root
```

### untuk masuk ke terimal container apache
```bash
docker exec -it marac-php-fpm bash
```

### untuk solo command
```bash
# Jika pakai Nginx
docker exec -it marac-php-fpm php artisan serve
docker compose exec php-fpm composer install
# untuk melihat log seperti php artisan serve
docker compose logs -f nginx php-fpm
# Jika pakai Apache
docker exec -it marac-apache php artisan serve
```

### untuk mematikan container
```bash
# untuk selesai code
docker compose down

# untuk rest bentar
docker compose stop

# untuk reset total dan ganti konfigurasi
docker compose down -v
docker compose --profile nginx down -v
```

### untuk new project laravel
```bash
docker compose exec apache composer create-project laravel/laravel:^13.0 .
# atau
docker compose exec apache composer create-project laravel/laravel .
```
### docker permission
```bash
# Masuk ke container PHP dan ubah owner folder ke user web (www-data)
sudo chown -R $USER:$USER .
docker exec -it marac-php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec -it marac-php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# bash docker
php artisan storage:link
chown -R www-data:www-data /var/www/html/public/
chmod -R 775 /var/www/html/public/

# add theme in filament
php artisan make:filament-theme
```
```bash
#db import
docker exec -i <nama_container_mysql> mysql -u <username_db> -p<password_db> <nama_database> < path/ke/file.sql

#db export
docker exec <nama_container_mysql> mysqldump -u <username_db> -p<password_db> <nama_database> > hasil_export.sql

```
## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
