### php_rabbit

#### Install

```
cd ./docker

docker-compose up --build -d

docker exec -it php_rabbit-cli composer require php-amqplib/php-amqplib
docker exec -it php_rabbit-cli composer require vlucas/phpdotenv

```

