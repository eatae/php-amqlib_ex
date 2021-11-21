<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$deadLetterExchange = 'dl_exchange';
$retryQueue         = 'retry_task';

$exchange           = 'task';
$queue              = 'task';

// Dead Letter eXchange and Queue
$channel->exchange_declare('dlx-exchange', 'direct', 'false', 'false', 'false');
$channel->queue_declare('dlx-queue', false, false, false, false);
$channel->queue_bind('dlx-queue', 'dlx-exchange');

# Regular eXchange and Queue
$channel->exchange_declare('r-exchange', 'direct', 'false', 'false', 'false');
$channel->queue_declare('r-queue', false, false, false, false, new AMQPTable(
    [
        'x-dead-letter-exchange' => 'dlx-exchange',
        'x-dead-letter-routing-key' => 'dlx',
        'x-message-ttl' => 50000,
        'x-expires' => 60000
    ]
));
$channel->queue_bind('r-queue', 'r-exchange');





echo " [*] Waiting for messages. To exit press CTRL+C\n";

/**
 * Callback for receive message
 * - sleep by dot .
 *
 */
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

// no_ack = false
$channel->basic_consume('04-queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}