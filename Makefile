include .env

.PHONY: help check-traefik image rebuild adminer database dev prod upgrade shell rootshell mysql logs

help: ## zeigt diese Hilfe an
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

check-traefik:
ifeq (,$(shell docker ps -f name=^traefik$$ -q))
	$(error docker container traefik is not running)
endif

image: ## Docker-Image ohne Cache neu bauen
	@echo "(Re)building docker image"
	docker build --no-cache -t ghcr.io/mind-hochschul-netzwerk/$(SERVICENAME):latest .

rebuild: ## Docker-Image bauen
	@echo "Rebuilding docker image"
	docker build -t ghcr.io/mind-hochschul-netzwerk/$(SERVICENAME):latest .

adminer: check-traefik ## Adminer starten
	docker compose up -d adminer

database: ## Datenbank-Container neu erstellen
	docker compose up -d --force-recreate db

dev: check-traefik ## Dev-Server starten
	@echo "Starting DEV Server"
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate --remove-orphans

prod: check-traefik ## Produktions-Server starten
	@echo "Starting Production Server"
	docker compose up -d --pull always --force-recreate --remove-orphans app

upgrade: ## git pull und Produktions-Server neu starten
	git pull
	make prod

shell: ## Shell im App-Container
	docker compose exec app sh

rootshell: ## Shell im App-Container (root)
	docker compose exec --user root app sh

mysql: ## MySQL-CLI in der Datenbank oeffnen
	@echo "docker compose exec db mariadb --user=user --password=\"...\" database"
	@docker compose exec db mariadb --user=user --password="$(MYSQL_PASSWORD)" database

logs: ## Logs aller Container verfolgen
	docker compose logs -f
