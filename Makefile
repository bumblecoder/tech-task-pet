DOCKER_COMPOSE=docker compose
UP_SERVICE=$(DOCKER_COMPOSE) build --no-cache && $(DOCKER_COMPOSE) up -d && sleep 5
RESTART_SERVICE=$(DOCKER_COMPOSE) restart
EXEC_PHP_FPM=$(DOCKER_COMPOSE) exec php-fpm
COMPOSER=bash -c "composer install"

all: help
build: ## Build and deploy project from scratch
	@echo "Build and bootstrap project..."
	$(UP_SERVICE)
	$(EXEC_PHP_FPM) $(COMPOSER)
composer:  ## Install/update/delete composer libraries
	@echo "Running composer..."
	$(EXEC_PHP_FPM) $(COMPOSER)
down: ## Shut down project but keep mysql db data
	@echo "Shutting down project..."
	$(DOCKER_COMPOSE) down --remove-orphans
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
