<?php

namespace RabbitLogger;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Silex\Application;


class Logger implements LoggerInterface
{
    private $connection;
    private $channel;
    private $queueName;

    public function __construct(AMQPStreamConnection $connection, AMQPChannel $channel, $queueName = 'logger')
    {
        $this->connection = $connection;
        $this->channel    = $channel;
        $this->queueName  = $queueName;

        $this->channel->queue_declare($queueName, false, false, false, false);
    }

    function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function emergency($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::EMERGENCY);
    }

    public function alert($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::ALERT);
    }

    public function critical($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::CRITICAL);
    }

    public function error($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::ERROR);
    }

    public function warning($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::WARNING);
    }

    public function notice($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::NOTICE);
    }

    public function info($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::INFO);
    }

    public function debug($message, array $context = [])
    {
        $this->sendLog($message, $context, LogLevel::DEBUG);
    }

    public function log($level, $message, array $context = [])
    {
        $this->sendLog($message, $context, $level);
    }

    private function sendLog($message, array $context = [], $level = LogLevel::INFO)
    {
        $msg = new AMQPMessage(json_encode([$message, $context, $level]), ['delivery_mode' => 2]);

        $this->channel->basic_publish($msg, '', $this->queueName);
    }
}