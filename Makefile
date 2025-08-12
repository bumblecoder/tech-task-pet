DOCKER_COMPOSE=docker compose
UP_SERVICE=$(DOCKER_COMPOSE) build --no-cache && $(DOCKER_COMPOSE) up -d && sleep 5
RESTART_SERVICE=$(DOCKER_COMPOSE) restart
EXEC_PHP_FPM=$(DOCKER_COMPOSE) exec php-fpm
COMPOSER=bash -c "composer install"
DATABASE=bash -c 'for i in `seq 1 10`; do bin/console doctrine:database:create --if-not-exists && break || sleep 2; done'
MIGRATIONS = bash -c "bin/console doctrine:migrations:status | grep -q 'New Migrations' && bin/console doctrine:migrations:migrate --no-interaction || echo 'No migrations to execute'"
WAIT_DB= \
  bash -c 'for i in `seq 1 60`; do nc -z mysql 3306 && echo "✅ MySQL ready" && exit 0 || echo "⏳ Waiting for MySQL..." && sleep 2; done && echo "❌ Timeout waiting for MySQL" && exit 1'
FIXTURES = bash -c "bin/console d:f:l --no-interaction"
all: help
build: ## Build and deploy project from scratch
	@echo "🚀 Building and bootstrapping the project..."
	$(UP_SERVICE)
	$(EXEC_PHP_FPM) $(WAIT_DB)
	$(EXEC_PHP_FPM) $(COMPOSER)
	$(EXEC_PHP_FPM) $(DATABASE)
	$(EXEC_PHP_FPM) $(MIGRATIONS)
	$(EXEC_PHP_FPM) $(FIXTURES)
composer: ## Install/update/delete composer libraries
	@echo "🎼 Installing composer packages..."
	$(EXEC_PHP_FPM) $(COMPOSER)
down: ## Shut down project but keep mysql db data
	@echo "🛑 Shutting down the project..."
	$(DOCKER_COMPOSE) down --remove-orphans
#tailwind: ## Compile Tailwind CSS
#	@echo "🎨 Compiling Tailwind CSS..."
#	$(DOCKER_COMPOSE) exec node npx tailwindcss -i ./assets/styles/app.css -o ./public/build/app.css
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
