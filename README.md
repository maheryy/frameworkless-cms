## CMS using PHP / MVC Model

**Description**


## Get started

First, you need to copy **.env.dist into a .env file** in the root directory

**Make sure you have Docker (and docker-compose) installed and running**

Run with :

```bash
docker-compose up -d
```

Stop with :

```bash
docker-compose down
```

#### You can create another docker-compose file with the following format :

*docker-compose.[custom_format].yaml* - (ignored by git)

#### Run with custom docker-compose :

*file 'docker-compose.dev.yaml' (without phpMyAdmin container)*
```bash
docker-compose -f "docker-compose.dev.yaml" up -d
```