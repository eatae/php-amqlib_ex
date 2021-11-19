<?php

namespace Src;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitConnection
{
    protected static AMQPStreamConnection $connect;

    /**
     * @param string|mixed $host
     * @param string|mixed $port
     * @param string|mixed $user
     * @param string|mixed $pass
     * @return AMQPStreamConnection
     */
    public static function getConnection(string $host = RABBITMQ_HOST, string $port = RABBITMQ_PORT, string $user = RABBITMQ_USER, string $pass = RABBITMQ_USER): AMQPStreamConnection
    {
        if (empty(self::$connect)) {
            self::$connect = new AMQPStreamConnection($host, $port, $user, $pass);
        }

        return self::$connect;
    }

    private function __construct(){}
    private function __clone(){}
}