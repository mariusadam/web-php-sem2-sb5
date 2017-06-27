<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use WebApp\Controller\AdminController;
use WebApp\Controller\AjaxController;
use WebApp\Controller\IndexController;
use WebApp\Repository\EventRepository;
use WebApp\Repository\UserRepository;
use WebApp\Services\EntityBuilder;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new DoctrineServiceProvider(), [
        'db.options' => [
            'host'     => '172.19.0.2',
            'port'     => '3306',
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8mb4',
            'dbname'   => 'web_db',
            'user'     => 'web_user',
            'password' => 'abcd1234',
        ],
    ]
);

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
}
);

$controllers = [
    IndexController::class,
    AjaxController::class,
    AdminController::class,
];

foreach ($controllers as $controllerClass) {
    $app[$controllerClass] = function () use ($app, $controllerClass) {
        return new $controllerClass($app);
    };
}

$app[EntityBuilder::class] = function () {
    return new EntityBuilder();
};

$repos = [
    UserRepository::class,
    EventRepository::class,
];

foreach ($repos as $repo) {
    $app[$repo] = function () use ($app, $repo) {
        return new $repo(
            $app['db'], $app[EntityBuilder::class]
        );
    };
}

return $app;
