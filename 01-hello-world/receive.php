<?php
require_once realpath(__DIR__ .'/../vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Dotenv\Dotenv;

// load .env file
$config = Dotenv::createImmutable(realpath(__DIR__ .'/../'));
$config->load();
// connection and queue declare
$connection = new AMQPStreamConnection(
    $_ENV['RABBITMQ_HOST'],
    $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USER'],
    $_ENV['RABBITMQ_PASS']
);
$channel = $connection->channel();
$channel->queue_declare('test', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";


// callback for receive message
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('test', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}