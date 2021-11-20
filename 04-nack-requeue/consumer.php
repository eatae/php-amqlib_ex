<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use Src\RabbitConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$connection = RabbitConnection::getConnection();
$channel = $connection->channel();

$channel->queue_declare('04-queue', false, false, false, false, new AMQPTable(
    [
        'x-dead-letter-exchange' => 'test-nack',
        'x-dead-letter-routing-key' => '04-queue-nack',
        'x-message-ttl' => 50000,
        'x-expires' => 60000
    ]
));
$channel->queue_declare('04-queue-nack', false, false, false, false);

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
        sleep(2);
        $msg->ack();

    } else {
        echo " [x] Nack message\n";
        sleep(2);
        $msg->nack();
    }
};

// no_ack = false
$channel->basic_consume('04-queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}