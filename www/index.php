<?php

include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use RabbitLogger\LoggerServiceProvider;
use Silex\Application;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\KernelEvents;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel    = $connection->channel();

$app = new Application(['debug' => true]);

$app->register(new LoggerServiceProvider($connection, $channel));

$app->on(KernelEvents::TERMINATE, function (Event\PostResponseEvent $event) use ($app) {
    $app['rabbit.logger']->info('TERMINATE');
});

$app->on(KernelEvents::CONTROLLER, function (Event\FilterControllerEvent $event) use ($app) {
    $app['rabbit.logger']->info('CONTROLLER');
});

$app->on(KernelEvents::EXCEPTION, function (Event\GetResponseForExceptionEvent $event) use ($app) {
    $app['rabbit.logger']->info('EXCEPTION');
});

$app->on(KernelEvents::FINISH_REQUEST, function (Event\FinishRequestEvent $event) use ($app) {
    $app['rabbit.logger']->info('FINISH_REQUEST');
});

$app->on(KernelEvents::RESPONSE, function (Event\FilterResponseEvent $event) use ($app) {
    $app['rabbit.logger']->info('RESPONSE');
});

$app->on(KernelEvents::REQUEST, function (Event\GetResponseEvent $event) use ($app) {
    $app['rabbit.logger']->info('REQUEST');
});

$app->on(KernelEvents::VIEW, function (Event\GetResponseForControllerResultEvent $event) use ($app) {
    $app['rabbit.logger']->info('VIEW');
});

$app->get('/', function (Application $app) {
    $app['rabbit.logger']->info('inside route');

    return "HELLO";
});

$app->run();