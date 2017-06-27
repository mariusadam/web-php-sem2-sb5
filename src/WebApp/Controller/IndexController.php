<?php

namespace WebApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use WebApp\Entity\Event;

/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/26/17
 * Time: 8:50 PM
 */
class IndexController extends BaseController
{
    public function indexAction()
    {
        if ($this->getLoggedUser() != null) {
            return $this->redirect('admin_page');
        }
        return $this->render('index.html.twig');
    }

    public function loginAction(Request $request)
    {
        if ($this->getLoggedUser() != null) {
            return $this->redirect('admin_page');
        }
        try {
            $username = $request->request->get('username');
            $this->getSession()->set('last_username', $username);
            $user = $this
                ->getUserRepository()
                ->findByUsernameAndPassword(
                    $username,
                    $request->request->get('password')
                );
            $this->saveLoggedUser($user);
            $this->getSession()->getFlashBag()
                ->add('success', 'Logged in.');
            return $this->redirect('admin_page');
        } catch (\Exception $e) {
            $this
                ->getSession()
                ->getFlashBag()
                ->add('error', $e->getMessage());
            return $this->indexAction();
        }
    }

    public function logoutAction()
    {
        $this->getSession()->remove('logged_user_id');
        return $this->redirect('homepage');
    }

    public function listEventsAction()
    {
        $dates = $this->getEventRepository()->getDistinctDates();
        $tabs = [];
        $closestDate = reset($dates);
        $today = new \DateTime();
        foreach ($dates as $date) {
            if ($date > $closestDate && $date <= $today) {
                $closestDate = $date;
            }
        }
        foreach ($dates as $date) {
            $tab['date'] = $date->format('Y-m-d');
            $tab['active'] = '';
            $tab['events'] = [];
            if ($tab['date'] == $closestDate->format('Y-m-d')) {
                $tab['active'] = 'active';
                $tab['events'] = $this->getEventsForDate($tab['date']);
            }

            $tabs[] = $tab;
        }

        return $this->render('admin/tabs.html.twig', [
            'tabs' => $tabs
        ]);
    }

    protected function getEventsForDate($date)
    {
        $events = $this->getEventRepository()->getAll();
        $asArray = [];
        /** @var Event $event */
        foreach ($events as $event) {
            if ($event->getDate()->format('Y-m-d') == $date) {
                $item['id'] = $event->getId();
                $item['date'] = $event->getDate()->format('Y-m-d H:i:s');
                $item['date_timestamp'] = $event->getDate()->getTimestamp();
                $item['description'] = $event->getDescription();
                $asArray[] = $item;
            }
        }
        return $asArray;
    }

    public function getEventsAction($date)
    {
        try {
            $result['status'] = 'success';
            $result['events'] = $this->getEventsForDate($date);
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        }

        return $this->json($result);
    }
}