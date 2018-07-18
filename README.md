# Random API

## Requirements

* Web server (like nginx)
* Mysql or Mariadb server
* SMTP server or a mail catch (like maildev)

## Installation
If you're using docker please run this :

```bash
cp .env.dist .env
docker-compose up -d --build
docker-compose exec php bash
```
Update `.env` with your informations (if using docker leave default)

## Setup

In your php container (`docker-compose exec php bash`)
```
composer install
php bin/console doctrine:schema:update --force
# /!\ check that you have a valid TMDB_API_KEY in your .env
php bin/console movie:import
php bin/console setup:achievements
php bin/console setup:admin
```
