##@ Invoicing API Challenge

help:  ## Display help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-30s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

CONTAINER = invoices-api

start: ## Start project
	docker-compose up --build -d

stop: ## Stop project
	docker-compose stop

down: ## Remove docker containers and network
	docker-compose down

logs: ## View project logs
	docker logs -f $(CONTAINER)

terminal: ## Run terminal
	docker exec -it $(CONTAINER) bash

terminal-root: ## Run terminal as root
	docker exec -it $(CONTAINER) bash

clear-cache: ## Clear and warm up cache
	docker exec -it $(CONTAINER) sh -c "bin/console cache:clear"

format: ## Run php cs fixer
	docker exec -it $(CONTAINER) sh -c "php-cs-fixer fix --config .php-cs-fixer.dist.php src tests"

phpstan: ## Run static code analyser
	docker exec -it $(CONTAINER) sh -c "./vendor/bin/phpstan analyse --memory-limit=4G"

security-check: ## Run security check
	docker exec -it $(CONTAINER) sh -c "local-php-security-checker --update-cache && local-php-security-checker --no-dev"

run-tests: ## Run all tests
	docker exec -it $(CONTAINER) sh -c "bin/phpunit"

unit-tests: ## Run all tests
	docker exec -it $(CONTAINER) sh -c "bin/phpunit --testsuite=Unit"

integration-tests: ## Run all tests
	docker exec -it $(CONTAINER) sh -c "bin/phpunit --testsuite=Integration"

functional-tests: ## Run all tests
	docker exec -it $(CONTAINER) sh -c "bin/phpunit --testsuite=Functional"

