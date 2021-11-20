<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$channel->queue_declare('03-queue', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";


/**
 * Callback for receive message
 * - sleep by dot .
 */
$callback = function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    //echo " [x] Done\n";
    echo " [x] Reject message\n";
    $msg->reject();
};

// no_ack = false
$channel->basic_consume('03-queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}