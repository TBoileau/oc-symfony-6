DISABLE_XDEBUG=XDEBUG_MODE=off

env ?= dev
db-driver ?= mysql
db-user ?= root
db-password ?= password
db-host ?= 127.0.0.1
db-port ?= 3306
db-name ?= oc-symfony-6
db-version ?= 8.0
db-charset ?= utf8mb4

install: ## Installation du projet
	composer install
	yarn install
	make prepare env=dev db-user=$(db-user) db-password=$(db-password) db-name=$(db-name) db-host=$(db-host) db-port=$(db-port) db-version=$(db-version) db-charset=$(db-charset)
	make prepare env=test db-user=$(db-user) db-password=$(db-password) db-name=$(db-name) db-host=$(db-host) db-port=$(db-port) db-version=$(db-version) db-charset=$(db-charset)
	make db env=dev
	make db env=test
.PHONY: install

prepare: ## Préparation du projet
	cp .env.dist .env.$(env).local
	sed -i -e 's/db-driver/$(db-driver)/' .env.$(env).local
	sed -i -e 's/db-user/$(db-user)/' .env.$(env).local
	sed -i -e 's/db-password/$(db-password)/' .env.$(env).local
	sed -i -e 's/db-name/$(db-name)/' .env.$(env).local
	sed -i -e 's/db-host/$(db-host)/' .env.$(env).local
	sed -i -e 's/db-port/$(db-port)/' .env.$(env).local
	sed -i -e 's/db-version/$(db-version)/' .env.$(env).local
	sed -i -e 's/db-charset/$(db-charset)/' .env.$(env).local
	sed -i -e 's/env/$(env)/' .env.$(env).local
	make db env=$(env)
.PHONY: prepare

db-fixtures: ## Chargement des fixtures
	php bin/console doctrine:fixtures:load -n --env=$(env)
.PHONY: db-fixtures

db-schema: ## Création du schéma de la base de données
	$(DISABLE_XDEBUG) php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	$(DISABLE_XDEBUG) php bin/console doctrine:database:create --env=$(env)
	$(DISABLE_XDEBUG) php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration --env=$(env)
.PHONY: db-schema

db-migration: ## Création d'une migration
	$(DISABLE_XDEBUG) php bin/console make:migration
.PHONY: db-migration

db: ## Création du schéma de la base de données et chargement des fixtures
	make db-schema env=$(env)
	make db-fixtures env=$(env)
.PHONY: db

tests: ## Lancement des tests
	$(DISABLE_XDEBUG) php bin/phpunit --testdox
.PHONY: tests

qa-phpstan: ## Analyse du code avec PHPStan
	$(DISABLE_XDEBUG) php vendor/bin/phpstan analyse -c phpstan.neon
.PHONY: qa-phpstan

qa-cs-fixer: ## Analyse du code avec PHP-CS-Fixer
	$(DISABLE_XDEBUG) php vendor/bin/php-cs-fixer fix --dry-run
.PHONY: qa-cs-fixer

qa-composer: ## Analyse du fichier composer.json
	composer valid
.PHONY: qa-composer

qa-doctrine: ## Analyse du mapping Doctrine
	$(DISABLE_XDEBUG) php bin/console doctrine:schema:valid --skip-sync
.PHONY: qa-doctrine

qa-twig: ## Analyse des templates Twig
	$(DISABLE_XDEBUG) php bin/console lint:twig templates
.PHONY: qa-twig

qa-yaml: ## Analyse des fichiers YAML
	$(DISABLE_XDEBUG) php bin/console lint:yaml config --parse-tags
.PHONY: qa-yaml

qa-container: ## Analyse du container Symfony
	$(DISABLE_XDEBUG) php bin/console lint:container
.PHONY: qa-container

qa-security-check: ## Analyse des vulnérabilités de sécurité
	symfony check:security
.PHONY: qa-security-check

qa-phpmd: ## Analyse du code avec PHPMD
	$(DISABLE_XDEBUG) php vendor/bin/phpmd src text .phpmd.xml
.PHONY: qa-phpmd

qa-phpcpd: ## Analyse du code avec PHPCPD
	$(DISABLE_XDEBUG) php vendor/bin/phpcpd src
.PHONY: qa-phpcpd

qa: ## Analyse du code
	make qa-composer
	make qa-doctrine
	make qa-twig
	make qa-yaml
	make qa-container
	make qa-security-check
	make qa-phpmd
	make qa-phpcpd
	make qa-cs-fixer
	make qa-phpstan
.PHONY: qa

fix-cs-fixer: ## Correction automatique des erreurs de code avec PHP-CS-Fixer
	$(DISABLE_XDEBUG) php vendor/bin/php-cs-fixer fix
.PHONY: fix-cs-fixer

fix: ## Correction automatique des erreurs de code
	make fix-cs-fixer
.PHONY: fix

help: ## Show this help.
	@echo "Symfony-And-Docker-Makefile"
	@echo "---------------------------"
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help