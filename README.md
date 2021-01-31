## CMS using PHP / MVC Model

**Description**


## Get started

Make sure to have Docker installed and running first.

Then, you need to create a **.env file in the root directory**

```bash
# .env file

# Default port for docker containers
SERVER_PORT=80
DB_PORT=3306
PHP_ADMIN_PORT=8888

# Database informations
DB_DRIVER=mysql
DB_HOST=database_cms
# You can change these variables below
DB_NAME=default_db
DB_ROOT_PWD=root
DB_USER=root
DB_PWD=root

```

Then you can run the app :

```bash
docker-compose up -d
```

Stop the app :

```bash
docker-compose down
```

#### You can create another docker-compose file with the following format :

*docker-compose.[custom_format].yaml* - **(ignored by git)**

#### Run with custom docker-compose :

*file 'docker-compose.dev.yaml' (without phpMyAdmin container)*
```bash
docker-compose -f "docker-compose.dev.yaml" up -d
```