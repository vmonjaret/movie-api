# Random API

## Requirements

* Web server (like nginx)
* Mysql or Mariadb server
* SMTP server or a mail catch (like maildev)

## Installation
If you're using docker please run this :

```bash
docker-compose up -d --build
docker-compose exec php bash
```

Now if you don't use docker it'll be the same flow :

1. Update `.env` with your informations (if using docker leave default)
2. Install dependencies : `composer install`

// TO COMPLETE