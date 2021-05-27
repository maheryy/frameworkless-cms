include .env

## Build the dockerfile project
build:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) build

## Run all docker services
start:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d

## Stop all docker services
stop:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) stop

## Restart all docker services
restart:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) stop
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d

## Show logs for all services
logs:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) logs --tail=5 -f

## Clear all services
clear:
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) down