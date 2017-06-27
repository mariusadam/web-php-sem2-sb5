<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebApp\Controller\AdminController;
use WebApp\Controller\AjaxController;
use WebApp\Controller\IndexController;

//Request::setTrustedProxies(array('127.0.0.1'));

function _ctrl($class, $action)
{
    return sprintf('%s:%sAction', $class, $action);
}

/** @var \Silex\Application $app */
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

$app->get('/', _ctrl(IndexController::class, 'index'))
    ->bind('homepage');

$app->get('ajax-test', _ctrl(AjaxController::class, 'test'));


$app->match('/login', _ctrl(IndexController::class, 'login'))
    ->method('GET|POST')
    ->bind('login_user');

$app->get('/admin', _ctrl(AdminController::class, 'index'))
    ->bind('admin_page');

$app->post('/logout', _ctrl(IndexController::class, 'logout'))
    ->bind('logout_user');

$app->get('/events', _ctrl(IndexController::class, 'listEvents'))
    ->bind('list_events');

$app->get('/events/for/{date}', _ctrl(IndexController::class, 'getEvents'))
    ->bind('events_for_date');

$app->post('/events/new', _ctrl(AdminController::class, 'createEvent'))
    ->bind('create_event');