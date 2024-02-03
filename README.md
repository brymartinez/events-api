## Running the app
```bash
docker compose up
cp .env.example .env # Edit according to `docker-compose.yml` credentials
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --class=UsersSeeder
php artisan serve
```

## Considerations
 - `Carbon` takes care of the monthly/weekly problem.
 - No unit tests were created.
