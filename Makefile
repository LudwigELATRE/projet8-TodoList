# Makefile - commandes utiles pour ToDo & Co

start:
	symfony serve --no-tls -d
	@echo "✅ Serveur lancé sur http://localhost:8000"

stop:
	symfony server:stop
	@echo "🛑 Serveur arrêté"

migrate:
	docker-compose exec $(DOCKER_PHP) php bin/console doctrine:migrations:migrate --no-interaction
	@echo "✅ Migrations exécutées"

start-db:
	docker-compose up -d
	@echo "✅ Conteneurs lancés. Accédez à l'app avec 'make server' ou via votre navigateur."

test:
	php bin/console hautelook:fixtures:load --env=test --no-interaction
	@echo "✅ La base test a été remplie avec des données de test"
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html var/coverage
	@echo "✅ Rapport de couverture généré dans var/coverage/index.html"
