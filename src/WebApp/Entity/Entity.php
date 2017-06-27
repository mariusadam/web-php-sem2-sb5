<?php

namespace WebApp\Entity;

/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/27/17
 * Time: 7:57 AM
 */
class Entity
{
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}