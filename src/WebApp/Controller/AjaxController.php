<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/26/17
 * Time: 9:00 PM
 */

namespace WebApp\Controller;


class AjaxController extends BaseController
{
    public function testAction($date)
    {
        return $this->json([
            'works' => true,
        ]);
    }
}