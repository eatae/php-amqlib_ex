<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$channel->queue_declare('04-queue-nack', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";


/**
 * Callback for receive message
 */
$callback = function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
    //echo " [x] Done\n";
    echo " [x] Done receive message from 04-queue-nack\n";
    // ack consume
    $msg->ack();
};

// no_ack = false
$channel->basic_consume('04-queue-nack', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}