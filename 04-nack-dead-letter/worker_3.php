<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$exchange           = 'task';
$queue              = 'task';
$deadLetterExchange = 'retry';
$retryQueue         = 'retry_task';
$channel->exchange_declare($exchange, 'direct', false, true);
$channel->exchange_declare($deadLetterExchange, 'direct', false, true);
// Normal queue
$channel->queue_declare($queue, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
    'x-dead-letter-exchange' => '',
    'x-dead-letter-routing-key' => $retryQueue
]));
$channel->queue_bind($queue, $exchange);
// Retry queue with TTL
$channel->queue_declare($retryQueue, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
    'x-dead-letter-exchange' => '',
    'x-dead-letter-routing-key' => $queue,
    'x-message-ttl' => 5000
]));
$channel->queue_bind($retryQueue, $deadLetterExchange);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


$callback = function ($msg) use ($channel) {
    echo " [x] Received ", $msg->body, " on ", date('Y-m-d, H:i:s'),"\n";
    echo " [-] Cannot process crap. Nacking message. \n";
    $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
};


$channel->basic_qos(null, 1, null);
$channel->basic_consume($queue, '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();