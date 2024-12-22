initial-setup:
	@make build
	@make up 
	@make composer-install
setup:
	@make build
	@make up 
	@make composer-update
	@make migrate
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up -d
composer-install:
	docker exec backend_app bash -c "composer create-project laravel/laravel . 11.x"
	docker exec backend_app bash -c "composer install"
	docker exec backend_app bash -c "php artisan key:generate"
	docker exec backend_app bash -c "php artisan install:api"
composer-update:
	docker exec backend_app bash -c "composer update"
migrate:
	docker exec backend_app bash -c "php artisan migrate"
migrate-seed:
	docker exec backend_app bash -c "php artisan migrate:fresh --seed"
	@make articles-sync
test:
	docker exec backend_app bash -c "php artisan test"
route-list:
	docker exec backend_app bash -c "php artisan route:list"
schedule-list:
	docker exec backend_app bash -c "php artisan schedule:list"
articles-sync:
	docker exec backend_app bash -c "php artisan app:article-sync-new-york-times"
	docker exec backend_app bash -c "php artisan app:article-sync-news-api-dot-org"
	docker exec backend_app bash -c "php artisan app:article-sync-news-api-ai"
