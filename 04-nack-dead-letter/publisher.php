<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use PhpAmqpLib\Message\AMQPMessage;
use Src\RabbitConnection;
use PhpAmqpLib\Wire\AMQPTable;

/**
 * Obtain CLI param
 */
$data = $argv[1];

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

if (empty($data)) {
    $data = "good";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, $exchange, $queue);

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();

