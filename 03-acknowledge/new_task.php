<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use PhpAmqpLib\Message\AMQPMessage;
use Src\RabbitConnection;

$data = implode(' ', array_slice($argv, 1));

$connection = RabbitConnection::getConnection();

$channel = $connection->channel();
$channel->queue_declare('03-queue', false, false, false, false);

if (empty($data)) {
    $data = "Hello World!";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', '03-queue');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();

