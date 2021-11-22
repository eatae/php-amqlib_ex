<?php
require_once realpath(__DIR__ .'/../bootstrap.php');

use PhpAmqpLib\Message\AMQPMessage;
use Src\RabbitConnection;

/**
 * Obtain CLI param
 */
$data = $argv[1];

$connection = RabbitConnection::getConnection();

$channel = $connection->channel();
$channel->queue_declare('r-queue', false, false, false, false);

if (empty($data)) {
    $data = "good";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', 'task');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();

