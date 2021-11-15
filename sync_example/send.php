<?php
require_once realpath(__DIR__ .'/../vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Dotenv\Dotenv;

// load .env file
$config = Dotenv::createImmutable(realpath(__DIR__ .'/../'));
$config->load();

$connection = new AMQPStreamConnection(
    $_ENV['RABBITMQ_HOST'],
    $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USER'],
    $_ENV['RABBITMQ_PASS']
);
$channel = $connection->channel();

$channel->queue_declare('test', false, false, false, false);

for ($i=0; $i <= 100; $i++) {
    $msg = new AMQPMessage('Hello World! ' . $i);
    $channel->basic_publish($msg, '', 'test');
}


echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();