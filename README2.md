## Running the app
```bash
docker compose up
cp .env.example .env # Edit according to `docker-compose.yml` credentials
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```
