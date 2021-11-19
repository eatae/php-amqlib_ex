### php_rabbit

#### Install

```
cd ./docker

docker-compose up --build -d

docker exec -it php_rabbit-cli composer require php-amqplib/php-amqplib
docker exec -it php_rabbit-cli composer require vlucas/phpdotenv
```

### Start RabbitMQ Management
* http://localhost:15672
* guest
* guest



run debug:
```
docker exec -it php_rabbit-cli php debug.php
```

### Hello World
- используем Default обменник (не создаём его).
- просто отправляем сообщение и читаем его в получателе.
<br>

run:
```
docker exec -it php_rabbit-cli php receive.php

docker exec -it php_rabbit-cli php send.php
```

### Work queue
- routing key при отправке сообщения это имя очереди.
- 
<br>

run:
```
docker exec -it php_rabbit-cli php 02-work-queue/worker.php

docker exec -it php_rabbit-cli php 02-work-queue/new_task.php "A very hard task which takes two seconds.."
```

