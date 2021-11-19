<?php
const CORE_PATH = __DIR__.'/';
require_once realpath(CORE_PATH.'vendor/autoload.php');

use Dotenv\Dotenv;

// load .env file
$config = Dotenv::createImmutable(realpath(CORE_PATH));
$config->load();

// Rabbit conf
define("RABBITMQ_HOST", $_ENV['RABBITMQ_HOST']);
define("RABBITMQ_PORT", $_ENV['RABBITMQ_PORT']);
define("RABBITMQ_USER", $_ENV['RABBITMQ_USER']);
define("RABBITMQ_PASS", $_ENV['RABBITMQ_PASS']);


/**
 * @param ...$params
 */
function sd(...$params)
{
    foreach ($params as $param) {
        var_dump($param);
    }
    exit();
}

/**
 * @param ...$params
 */
function s(...$params)
{
    foreach ($params as $param) {
        var_dump($param);
    }
}