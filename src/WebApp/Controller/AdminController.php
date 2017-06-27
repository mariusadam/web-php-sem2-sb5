<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/27/17
 * Time: 8:37 AM
 */

namespace WebApp\Controller;


use Symfony\Component\HttpFoundation\Request;
use WebApp\Entity\Event;
use WebApp\Repository\EventRepository;
use WebApp\Services\EntityBuilder;

class AdminController extends BaseController
{
    public function indexAction()
    {
        if ($this->getLoggedUser() == null) {
            $this->getSession()->getFlashBag()->add('error', 'You must login first.');
            return $this->redirect('homepage');
        }
        return $this->render('admin/index.html.twig');
    }

    public function createEventAction(Request $request)
    {
        try {
            $postData = $request->request->get('event');
            $event = new Event();
            $event->setDescription($postData['description']);
            $event->setDate(\DateTime::createFromFormat(
                EntityBuilder::DATETIME_FORMAT,
                sprintf('%s %s', trim($postData['date']), trim($postData['time']))
            ));
            $this->getEventRepository()->save($event);
            $this->getSession()->getFlashBag()->add('success', 'Event added');
        } catch (\Exception $e) {
            $this->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect('admin_page');
    }
}