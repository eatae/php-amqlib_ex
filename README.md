### php_rabbit

#### Install

```bash
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
```bash
docker exec -it php_rabbit-cli php debug.php
```


### Hello World
- используем Default обменник (не создаём его).
- просто отправляем сообщение и читаем его в получателе.
<br>

run:
```bash
docker exec -it php_rabbit-cli php receive.php

docker exec -it php_rabbit-cli php send.php
```


### Routing key
- routing key при отправке сообщения это имя очереди.
- можно ещё запустить несколько worker.php - тогда увидим как распределяются между ними сообщения.
<br>

run:
```bash
# смотрим в двух воркерах как распределяются задачи
docker exec -it php_rabbit-cli php 02-routing-key/worker.php
docker exec -it php_rabbit-cli php 02-routing-key/worker.php
# этот воркер покажет что в другую очередь писали сообщения по другому routing-key
docker exec -it php_rabbit-cli php 02-routing-key/worker-test.php

# эту команду запускаем несколько раз
docker exec -it php_rabbit-cli php 02-routing-key/new_task.php "A very hard task which takes two seconds...."
```


### Acknowledge
- подтверждаем получение и валидную обработку сообщения
  <br>

run:
```bash
# здесь ничего не происходит, сообщение отправлено, но worker его не выводит
docker exec -it php_rabbit-cli php 03-acknowledge/worker_noack.php

docker exec -it php_rabbit-cli php 03-acknowledge/worker_nack.php
docker exec -it php_rabbit-cli php 03-acknowledge/worker_ack.php

# эту команду запускаем несколько раз
docker exec -it php_rabbit-cli php 03-acknowledge/new_task.php "A very hard task which takes two seconds...."

# смотрим noack сообщения
docker exec -it php_rabbit-cli rabbitmqctl list_queues name messages_ready messages_unacknowledged
```



































