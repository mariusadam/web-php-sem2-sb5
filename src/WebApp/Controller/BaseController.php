<?php

namespace WebApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use WebApp\Entity\User;
use WebApp\Repository\EventRepository;
use WebApp\Repository\UserRepository;

/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/26/17
 * Time: 8:48 PM
 */
class BaseController
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $templateName
     * @param array $context
     *
     * @return string
     */
    protected function render($templateName, array $context = [])
    {
        return $this->get('twig')->render($templateName, $context);
    }

    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    protected function get($serviceName)
    {
        return $this->app[$serviceName];
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function json(array $data, $status = 200, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->get(UserRepository::class);
    }

    protected function saveLoggedUser(User $user)
    {
        $this
            ->getSession()
            ->set('logged_user_id', $user->getId());
    }

    protected function getLoggedUser()
    {
        try {
            return $this
                ->getUserRepository()
                ->findBy($this->getSession()->get('logged_user_id'));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * @param string $route
     * @param array  $params
     *
     * @return RedirectResponse
     */
    protected function redirect($route, $params = [])
    {
        return new RedirectResponse(
            $this->getUrlGenerator()->generate($route, $params)
        );
    }

    /**
     * @return UrlGenerator
     */
    protected function getUrlGenerator()
    {
        return $this->app['url_generator'];
    }

    /**
     * @return EventRepository
     */
    protected function getEventRepository()
    {
        return $this->get(EventRepository::class);
    }
}