check-traefik:
ifeq (,$(shell docker ps -f name=^traefik$$ -q))
	$(error docker container traefik is not running)
endif

.env:
	$(error file .env is missing, see .env.sample)

image:
	@echo "(Re)building docker image"
	docker build --no-cache -t mindhochschulnetzwerk/aufnahme:latest .

quick-image:
	@echo "Rebuilding docker image"
	docker build -t mindhochschulnetzwerk/aufnahme:latest .

dev: .env check-traefik
	@echo "Starting DEV Server"
	docker-compose -f docker-compose.base.yml -f docker-compose.dev.yml up -d --force-recreate

prod: image .env check-traefik
	@echo "Starting Production Server"
	docker-compose -f docker-compose.base.yml up -d --force-recreate

database: .env
	docker-compose -f docker-compose.base.yml up -d --force-recreate aufnahme-database
