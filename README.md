## Content Management System - Vanilla PHP / MVC Model

### Description

This project is a Content Management System built with vanilla PHP and Javascript using MVC model.\
A simple web based application to create your own website !

### Requirements

You will need to install [Docker](https://docs.docker.com/get-docker/) to run this app

### Get started

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