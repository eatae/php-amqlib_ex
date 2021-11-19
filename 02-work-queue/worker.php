<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$channel->queue_declare('02-queue', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";


/**
 * Callback for receive message
 * - sleep by dot .
 */
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
};

$channel->basic_consume('02-queue-test', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}