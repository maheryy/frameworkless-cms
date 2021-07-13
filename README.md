## CMS using PHP / MVC Model

### Description

***Development in progress...***

## Get started

**Make sure you have Docker (with docker-compose) installed and running**

First, run the command below to create a .env file\
(Run this once and change database informations)

```bash
make env 
OR 
cp .env.dist .env
```

\
Run with :

```bash
make start
OR
docker-compose up -d
```

\
Stop with :

```bash
make stop
OR
docker-compose stop
```

#### Run with custom docker-compose :

You can create another docker-compose file, *docker-compose.[custom_format].yaml*\
Then, change the DOCKER_COMPOSE_FILE variable to work with the new custom file