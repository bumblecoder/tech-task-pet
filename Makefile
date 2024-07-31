DOCKER_COMPOSE=docker compose
UP_SERVICE=$(DOCKER_COMPOSE) build --no-cache && $(DOCKER_COMPOSE) up -d && sleep 5
RESTART_SERVICE=$(DOCKER_COMPOSE) restart
EXEC_PHP_FPM=$(DOCKER_COMPOSE) exec php-fpm
EXEC_NODE=$(DOCKER_COMPOSE) exec node
COMPOSER=bash -c "composer install"
DATABASE=bash -c "php artisan db:create"
SEED=bash -c "php artisan migrate --seed"
NPM=sh -c "npm i"
NPX_MIX=sh -c "npx mix"
NODE_RUN_DEV=sh -c "npm run dev"

all: help
build: ## Build and deploy project from scratch
	@echo "Build and bootstrap project..."
	$(UP_SERVICE)
	$(EXEC_PHP_FPM) $(COMPOSER)
	$(EXEC_PHP_FPM) $(DATABASE)
	$(EXEC_PHP_FPM) $(SEED)
	$(EXEC_NODE) $(NPM)
	$(EXEC_NODE) $(NPX_MIX)
	$(EXEC_NODE) $(NODE_RUN_DEV)
composer:  ## Install/update/delete composer libraries
	@echo "Running composer..."
	$(EXEC_PHP_FPM) $(COMPOSER)
down: ## Shut down project but keep mysql db data
	@echo "Shutting down project..."
	$(DOCKER_COMPOSE) down --remove-orphans
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
