-include .env

## Run all docker services
start:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) up -d
	@echo Go to http://localhost:${SERVER_PORT}

## Build the dockerfile project
build:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) build

## Stop all docker services
stop:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) stop

## Restart all docker services
restart:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) stop
	@docker-compose -f $(DOCKER_COMPOSE_FILE) up -d

## Show logs for all services
logs:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) logs --tail=5

## Clear all services
clear:
	@docker-compose -f $(DOCKER_COMPOSE_FILE) down

## .env setup
env:
	@cp .env.dist .env
	@echo .env created !
	@chmod o+w app/config