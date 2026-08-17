.PHONY: clear

clear:
	docker exec -it marac-php-fpm bash -c "npm install && npm run build && php artisan config:clear && php artisan view:clear && php artisan cache:clear && php artisan route:clear && php artisan optimize:clear && php artisan optimize"
