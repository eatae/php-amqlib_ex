### php_rabbit

#### Install

```bash
cd ./docker

docker-compose up --build -d

docker exec -it php_rabbit-cli composer require php-amqplib/php-amqplib "^3.0"
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


### Ack / nack / reject / block
- подтверждаем получение и валидную обработку сообщения
  <br>

run:
```bash

# ack - сообщения удаляются из очереди
docker exec -it php_rabbit-cli php 03-acknowledge/worker_ack.php

# nack - сообщения так же удаляются из очереди либо переводится в очередь недоставленных сообщений (если очередь определена)
docker exec -it php_rabbit-cli php 03-acknowledge/worker_nack.php

# reject - отвергается обработка, сообщения остаются в очереди и циклически обрабатываются
docker exec -it php_rabbit-cli php 03-acknowledge/worker_reject.php

# все сообщения которые этот воркер обрабатывает блокируются и имеют состояние no ack,
# другие воркеры не могут работать с заблокированными сообщениями пока работает этот воркер
docker exec -it php_rabbit-cli php 03-acknowledge/worker_block.php

# эту команду запускаем несколько раз
docker exec -it php_rabbit-cli php 03-acknowledge/new_task.php "A very hard task which takes two seconds...."

# смотрим nack сообщения (rabbit container)
docker exec -it php_rabbit-rabbit rabbitmqctl list_queues name messages_ready messages_unacknowledged
```


### Nack (dead-letter-exchange)
- не подтвержденные сообщения переводим в очередь "неподтвержденных сообщений"
- параметр requeue должен быть false
- и должен быть обменник "dead-letter-exchange"
  <br>

run:
```bash
# включаем DLX для nack сообщений (политика) 
docker exec -it php_rabbit-rabbit rabbitmqctl set_policy DLX ".*" '{"dead-letter-exchange":"my-dlx"}' --apply-to queues

# ack/nack worker
docker exec -it php_rabbit-cli php 04-nack-dead-letter/worker.php
# or
docker exec -it php_rabbit-cli php 04-nack-dead-letter/worker_2.php

# это сообщение нормально обработается в своей очереди (ack)
docker exec -it php_rabbit-cli php 04-nack-dead-letter/publisher.php "good"

# это сообщение переведётся в очередь не валидных сообщений (nack)
docker exec -it php_rabbit-cli php 04-nack-dead-letter/publisher.php "bad"
```

































