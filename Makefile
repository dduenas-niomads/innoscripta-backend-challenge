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
composer-update:
	docker exec backend_app bash -c "composer update"
migrate:
	docker exec backend_app bash -c "php artisan migrate"
migrate-seed:
	docker exec backend_app bash -c "php artisan migrate:fresh --seed"
test:
	docker exec backend_app bash -c "php artisan test"