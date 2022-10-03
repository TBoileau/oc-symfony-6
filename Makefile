DISABLE_XDEBUG=XDEBUG_MODE=off

install: ## Installation du projet
	composer install
.PHONY: install

db-fixtures: ## Chargement des fixtures
	php bin/console doctrine:fixtures:load -n --env=$(env)

db-schema: ## Création du schéma de la base de données
	$(DISABLE_XDEBUG) php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	$(DISABLE_XDEBUG) php bin/console doctrine:database:create --env=$(env)
	$(DISABLE_XDEBUG) php bin/console doctrine:query:sql "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" --env=$(env)
	$(DISABLE_XDEBUG) php bin/console doctrine:migration:migrate --no-interaction --env=$(env)

db-migration: ## Création d'une migration
	$(DISABLE_XDEBUG) php bin/console make:migration

db: ## Création du schéma de la base de données et chargement des fixtures
	make database env=$(env)
	make fixtures env=$(env)

tests: ## Lancement des tests
	$(DISABLE_XDEBUG) php bin/phpunit --testdox

qa-phpstan: ## Analyse du code avec PHPStan
	$(DISABLE_XDEBUG) php vendor/bin/phpstan analyse -c phpstan.neon

qa-cs-fixer: ## Analyse du code avec PHP-CS-Fixer
	$(DISABLE_XDEBUG) php vendor/bin/php-cs-fixer fix --dry-run

qa-composer: ## Analyse du fichier composer.json
	composer valid

qa-doctrine: ## Analyse du mapping Doctrine
	$(DISABLE_XDEBUG) php bin/console doctrine:schema:valid --skip-sync

qa-twig: ## Analyse des templates Twig
	$(DISABLE_XDEBUG) php bin/console lint:twig templates

qa-yaml: ## Analyse des fichiers YAML
	$(DISABLE_XDEBUG) php bin/console lint:yaml config --parse-tags

qa-container: ## Analyse du container Symfony
	$(DISABLE_XDEBUG) php bin/console lint:container

qa-security-check: ## Analyse des vulnérabilités de sécurité
	symfony check:security

qa-phpmd: ## Analyse du code avec PHPMD
	$(DISABLE_XDEBUG) php vendor/bin/phpmd src text .phpmd.xml

qa-phpcpd: ## Analyse du code avec PHPCPD
	$(DISABLE_XDEBUG) php vendor/bin/phpcpd src

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
	make qa-psalm

fix-cs-fixer: ## Correction automatique des erreurs de code
	$(DISABLE_XDEBUG) php vendor/bin/php-cs-fixer fix

fix: ## Correction automatique des erreurs de code
	make fix-cs-fixer
