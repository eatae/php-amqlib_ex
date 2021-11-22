<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$exchange = '04_exchange';
$queue = '04_queue';
$deadLetterExchange = '04_dlExchange';
$retryQueue = '04_dlQueue';
$channel->exchange_declare($exchange, 'direct', false, true);
$channel->exchange_declare($deadLetterExchange, 'direct', false, true);
// Normal Queue
$channel->queue_declare($queue, false, true, false, false, false, new AMQPTable([
    'x-dead-letter-exchange' => $deadLetterExchange,
    'x-dead-letter-routing-key' => $retryQueue
]));
$channel->queue_bind($queue, $exchange, $queue);
// Retry queue with TTL
$channel->queue_declare($retryQueue, false, true, false, false, false, new AMQPTable([
    'x-dead-letter-exchange' => $exchange,
    'x-dead-letter-routing-key' => $queue,
    // через сколько будет возвращено обратно в исходную очередь (10 сек)
    'x-message-ttl' => 10000,
    // через сколько сообщение будет удалено вообще (100 сек)
    //'x-expires' => 100000
]));
$channel->queue_bind($retryQueue, $deadLetterExchange, $retryQueue);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
    if ($msg->body == "good") {
        echo " [x] Ack message\n";
        sleep(4);
        $msg->ack();

    } else {
        echo " [x] Nack message\n";
        sleep(4);
        $msg->nack();
    }
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume($queue, '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();