<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use PhpAmqpLib\Message\AMQPMessage;
use Src\RabbitConnection;

$data = implode(' ', array_slice($argv, 1));

$connection = RabbitConnection::getConnection();

$channel = $connection->channel();
$channel->queue_declare('02-queue', false, false, false, false);
$channel->queue_declare('02-queue-test', false, false, false, false);

if (empty($data)) {
    $data = "Hello World";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', '02-queue');
$channel->basic_publish($msg, '', '02-queue-test');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();

