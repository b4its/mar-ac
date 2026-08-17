# MARAC - Sistem Informasi Pemeliharaan Aset

## 🚀 Cara Menjalankan Aplikasi

### Prasyarat
- Docker Compose installed
- Port 8000 dan 5173 tersedia

### Menjalankan dengan Docker (Recommended)

```bash
# Build and start services with NGINX + PHP-FPM
docker-compose --profile nginx up --build -d

# Or use APACHE profile
docker-compose --profile apache up --build -d
```

### Mengakses Aplikasi

- **URL:** http://localhost:8000
- Default login credentials (see `.env.example` or database seeders)

### Stop & Clean Up

```bash
# Stop all containers
docker-compose down

# Stop and remove volumes (⚠️ This deletes database!)
docker-compose down -v
```

## 🔧 Troubleshooting

### Database Connection Issues
```bash
# Wait for database to be ready
docker-compose logs db

# Manually run migrations inside container
docker exec -it marac-php-fpm php artisan migrate:fresh --seed
```

### Clear Cache Inside Container
```bash
docker exec -it marac-php-fpm php artisan cache:clear
docker exec -it marac-php-fpm php artisan view:clear
docker exec -it marac-php-fpm php artisan config:clear
```

### View Logs
```bash
# Application logs
docker logs marac-php-fpm

# Web server logs
docker logs marac-nginx

# Database logs  
docker logs marac-db
```

## 📋 Service Structure

- **marac-db** (MySQL 8.4): Database server on port 3306
- **marac-php-fpm** (PHP 8.4): Backend processing
- **marac-nginx** (Nginx Alpin): Web server on port 8000

## 🔐 Default Login

Check seeder files in `database/seeders/` for default user credentials.

## 🎨 UI/UX Improvements Made

1. ✅ Fixed Bauhaus shape colors (red, yellow, blue)
2. ✅ Added focus states for buttons and inputs
3. ✅ Improved accessibility (ARIA labels, semantic HTML)
4. ✅ Fixed meta tags and theme color
5. ✅ Resolved JavaScript error in laporan saya page
6. ✅ Fixed controller query for different report types
7. ✅ Enhanced dark mode compatibility
8. ✅ Better responsive design
9. ✅ Consistent button styles
10. ✅ Proper form validation feedback

## 🛠 Development

### Compile Assets
```bash
docker exec -it marac-php-fpm npm install
docker exec -it marac-php-fpm npm run dev
# or
docker exec -it marac-php-fpm npm run build
```

### Artisan Commands
```bash
docker exec -it marac-php-fpm php artisan <command>
```

## 📝 Notes

- The application uses Tailwind CSS with custom Bauhaus design system
- All static assets are compiled via Vite
- Database data is seeded on first container startup
- Session driver uses database (ensure sessions table exists)
