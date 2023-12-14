.PHONY: help
.DEFAULT_GOAL = help

# —— Init variables 🐳  ———————————————————————————————————————————————————————————————
DOCKER = docker
DOCKER_RUN = $(DOCKER) run
# —— docker-composer ————————————————————————————
DOCKER_COMPOSE=@docker compose ${DOCKER_COMPOSE_FILES}
DOCKER_COMPOSE_UP = $(DOCKER_COMPOSE) up -d
#DOCKER_COMPOSE_UP =docker-compose up -d
DOCKER_COMPOSE_DOWN = $(DOCKER_COMPOSE) down
DOCKER_COMPOSE_STOP = $(DOCKER_COMPOSE) stop
DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE) exec
DOCKER_COMPOSE_RUN=$(DOCKER_COMPOSE) run
# —— php, composer, symfony console ————————————————————————————
DOCKER_COMPOSE_EXEC_PHP=$(DOCKER_COMPOSE_EXEC) php_apache
#COMPOSER=$(DOCKER_COMPOSE_EXEC_PHP) -d memory_limit=-1 /usr/local/bin/composer
COMPOSER=$(DOCKER_COMPOSE_EXEC_PHP) -d memory_limit=-1 composer
SYMFONY_CONSOLE=$(DOCKER_COMPOSE_EXEC_PHP) bin/console
# —— phpqa ————————————————————————————
PHPQA = jakzal/phpqa
PHPQA_RUN = $(DOCKER_RUN) --init -it --rm -v $(PWD):/project -w /project $(PHPQA)

# —— Detect Operating System and name it ————————————————————————————
OS_INFORMATION=$(shell uname -s)
ifneq (,$(findstring Linux,$(OS_INFORMATION)))
OS_NAME = linux
endif

ifneq (,$(findstring Darwin,$(OS_INFORMATION)))
OS_NAME = mac
endif

ifneq (,$(findstring CYGWIN,$(OS_INFORMATION)))
OS_NAME = win
endif

ifneq (,$(findstring MINGW,$(OS_INFORMATION)))
OS_NAME = win
endif

DOCKER_COMPOSE_FILES := -f docker-compose.yml

# —— Check for an OS-specific docker-compose file and include it if found. ————————————————————————————
ifneq ("$(wildcard docker-compose-${OS_NAME}.yml)","")
  DOCKER_COMPOSE_FILES := $(DOCKER_COMPOSE_FILES) -f docker-compose-${OS_NAME}.yml
endif

# Check for a local docker-compose file and include it if found. ————————————————————————————
ifneq ("$(wildcard docker-compose-local.yml)","")
  DOCKER_COMPOSE_FILES := $(DOCKER_COMPOSE_FILES) -f docker-compose-local.yml
endif

# Create the ..env file if it doesn't exist by copying the ..env.dist file. ————————————————————————————
.env:
ifeq (,$(wildcard ./.env))
	cp .env.dist .env
endif

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
build: ## Build project dependencies.
build: .env	start
	$(DOCKER_COMPOSE_EXEC_PHP) sh -c "./automation/bin/build.sh"

kill: ## Kill docker's containers.
kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE_DOWN) --volumes --remove-orphans

install: ## Start docker stack and install the project.
install: .env start
	$(DOCKER_COMPOSE_EXEC_PHP) sh -c "./automation/bin/install.sh"

update: ## Start docker stack and update the project.
update: .env start
	$(DOCKER_COMPOSE_EXEC_PHP) sh -c "./automation/bin/update.sh"

setup: ## Start docker stack, build and install the project.
setup: .env build install

reset: ## Kill docker's containers and start a fresh install of the project.
reset: kill setup

#start: update-permissions ## Start the project.
#	$(DOCKER_COMPOSE_UP) --remove-orphans

start: update-permissions ## Start the project.
	$(DOCKER_COMPOSE_UP) --remove-orphans
	@$(call GREEN, "The application is available on http://localhost:8080")
#	$(DOCKER_COMPOSE) exec -u 0 php sh -c "if [ -d /var/www/html/web/sites/default ]; then chmod -R a+w /var/www/html/web/sites/default; fi"
#	$(DOCKER_COMPOSE) exec -u 0 php sh -c "if [ -d /var/cache ]; then chmod -R a+w /var/cache; fi"

update-permissions: ## Fix permissions between Docker and the host.
ifeq ($(OS_NAME), linux)
update-permissions:
	sudo setfacl -dR -m u:$(shell whoami):rwX -m u:82:rwX -m u:1000:rwX .
	sudo setfacl -R -m u:$(shell whoami):rwX -m u:82:rwX -m u:1000:rwX .
else ifeq ($(OS_NAME), mac)
update-permissions:
	sudo dseditgroup -o edit -a $(shell id -un) -t user $(shell id -gn 82)
endif

stop:	## Stop docker's containers
	$(DOCKER_COMPOSE_STOP)

rm:	stop ## Remove docker's containers
	$(DOCKER_COMPOSE) rm -f

clean: ## Kill docker's containers and remove generated files
clean: kill
	rm -rf vendor

restart: rm start ## Restart docker's containers

# if the first word is "console", run the rest of the line as a command in the "php" service
ifeq (console,$(firstword $(MAKECMDGOALS)))
  # Extract words from the 2nd word
  CONSOLE_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  # Define those words as targets. Then, we can use them as arguments for the "console" target.
  $(eval $(CONSOLE_ARGS):;@:)
endif

console: ## Open a console in the container passed in argument (e.g make console php)
	$(DOCKER_COMPOSE_EXEC) $(CONSOLE_ARGS) bash

.PHONY: build kill install update setup reset start update-permissions stop rm clean restart console

## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
#vendor-install:	## Install vendors
#	$(COMPOSER) install
#
#vendor-update:	## Update vendors
#	$(COMPOSER) update

ifeq (composer,$(firstword $(MAKECMDGOALS)))
  COMPOSER_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(COMPOSER_ARGS):;@:)
endif
composer: ## Execute a composer command inside PHP container (e.g: make composer require drupal/paragraphs)
	$(COMPOSER) $(COMPOSER_ARGS)

clean-vendor: cc-hard ## Remove vendors' files and re-install
	$(DOCKER_COMPOSE_EXEC_PHP) rm -Rf vendor
	$(DOCKER_COMPOSE_EXEC_PHP) rm composer.lock
	$(COMPOSER) install

cc: ## Clear the cache
	$(SYMFONY_CONSOLE) c:c

cc-test: ## Clear the test environment cache
	$(SYMFONY_CONSOLE) c:c --env=test

cc-hard: ## Remove cache files
	$(DOCKER_COMPOSE_EXEC_PHP) rm -fR project/var/cache/*

clean-db: ## Re-initialize the database
	- $(SYMFONY_CONSOLE) d:d:d --force --connection
	$(SYMFONY_CONSOLE) d:d:c
	$(SYMFONY_CONSOLE) d:m:m --no-interaction
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

clean-db-test: cc-hard cc-test ## Re-initialize the database in test environment
	- $(SYMFONY_CONSOLE) d:d:d --force --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

.PHONY: cc cc-test cc-hard clean-db clean-db-test

## —— Tests ———————————————————————————————————————————————————————————————
test-unit: ## Launch unit tests
	$(DOCKER_COMPOSE_EXEC_PHP) project/bin/phpunit tests/Unit/

test-func: clean-db-test	## Launch functional tests
	$(DOCKER_COMPOSE_EXEC_PHP) project/bin/phpunit tests/Func/

tests: test-func test-unit	## Launch all tests

cs: ## Launch php code sniffer
	$(DOCKER_COMPOSE_EXEC_PHP) project/vendor/bin/phpcs -n

qa-cs-fixer-dry-run: ## Run php-cs-fixer in dry-run mode.
	$(PHPQA_RUN) php-cs-fixer fix --rules=@Symfony --verbose --dry-run .project/src
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Run php-cs-fixer.
	$(PHPQA_RUN) php-cs-fixer fix --rules=@Symfony --verbose .project/src/Twig/AppExtension.php
.PHONY: qa-cs-fixer

qa-phpstan: ## Run phpstan.
	$(PHPQA_RUN) phpstan analyse --level=0 .project/src
.PHONY: qa-phpstan

qa-rector-dry-run: ## Run rector.
	$(PHPQA_RUN) rector process --dry-run .project/src
.PHONY: qa-phpstan

qa-rector: ## Run rector.
	$(PHPQA_RUN) rector process ./project/src
.PHONY: qa-phpstan

qa-audit: ## Run composer audit.
	$(COMPOSER) audit
.PHONY: qa-audit


## —— NPM ———————————————————————————————————————————————————————————————
npm-logs: ## Show NPM logs.
	$(DOCKER_COMPOSE) logs --tail="20" -f npm

npm-install: ## install dependencies.
	$(DOCKER_COMPOSE_RUN) --rm npm npm install

npm-rundev: ## Start NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run dev

npm-watch: ## Run "watch" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run watch

npm-runbuild: ## Start NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run build

npm-lint: ## Run "lint" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run lint

npm-lint_fix: ## Run "lint_fix" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run lint_fix

npm-eslint: ## Run "eslint" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run eslint

npm-eslint_fix: ## Run "eslint_fix" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm run eslint_fix

npm-auditfix: ## Run "watch" command for NPM.
	$(DOCKER_COMPOSE_RUN) --rm npm npm audit fix

.PHONY: npm-logs npm-install npm-rundev npm-watch npm-runbuild npm-lint npm-lint_fix npm-eslint npm-eslint_fix npm-auditfix


phpstan: ## Run "phpstan" command.
	$(EXEC_PHP) vendor/bin/phpstan analyse src -n --memory-limit="-1"

.PHONY: phpstan

.DEFAULT_GOAL := help

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Commands list.
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
