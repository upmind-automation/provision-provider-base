# Typing 'make setup-php74' will start the dev environment with PHP 7.4 & xdebug
setup-php74: stop-containers --prep-dockerfile-php74 start-containers --remove-packages composer-install

# Get a shell on the PHP container
shell:
	docker compose exec -it app /bin/bash

# Run Static Analysis (PHPStan)
static-analysis:
	docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G

# Start the dev environment
start-containers:
	docker compose up -d --build

# Stop the dev environment
stop-containers:
	docker compose down

# Stop and remove all containers
kill-containers:
	docker compose kill
	docker compose rm --force

# Install composer dependencies
composer-install:
	docker compose exec app composer install --no-interaction

# Copy Dockerfile for PHP 7.4
--prep-dockerfile-php74: --remove-dockerfile --prep-docker-compose-file
	cp "./.docker/Dockerfile.php74" "./.docker/Dockerfile"

# Copy docker-compose.yml file
--prep-docker-compose-file:
	[ -f "./docker-compose.yml" ] || cp "./docker-compose.yml.example" "./docker-compose.yml"

# Remove Dockerfile
--remove-dockerfile:
	rm -f ./docker/Dockerfile

# Remove composer related files
--remove-packages: --remove-lockfile --remove-vendor

# Remove composer.lock file
--remove-lockfile:
	docker compose exec app rm -f ./composer.lock

# Remove vendor directory
--remove-vendor:
	docker compose exec app rm -rf ./vendor
