<?php

namespace RabbitLogger;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Silex\Application;
use Silex\ServiceProviderInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    private $connection;
    private $channel;

    public function __construct(AMQPStreamConnection $connection, AMQPChannel $channel)
    {
        $this->connection = $connection;
        $this->channel    = $channel;
    }

    public function register(Application $app)
    {
        $app['rabbit.logger'] = $app->share(
            function () use ($app) {
                $channelName = isset($app['logger.channel.name']) ? $app['logger.channel.name'] : 'logger.channel';

                return new Logger($this->connection, $this->channel, $channelName);
            }
        );
    }

    public function boot(Application $app)
    {
    }
}