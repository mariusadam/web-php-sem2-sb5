<?php

namespace WebApp\Services;

use WebApp\Entity\Entity;
use WebApp\Services\EntityBuilder;
use WebApp\Services\Utils;


/**
 * Class EntityManager
 *
 * @package App\Services
 *
 * @author  Marius Adam
 */
class EntityMapper
{
    /**
     * @var array
     */
    private static $entitiesProperties = [];

    /**
     * @param $entity
     *
     * @return array
     */
    public static function toArrayKeepCase($entity)
    {
        return self::toArray($entity, false);
    }

    /**
     * @param      $entity
     * @param bool $changeCase
     *
     * @return array
     */
    public static function toArray($entity, $changeCase = true)
    {
        $props = self::getProperties($entity);
        $entityData = [];
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $value = $prop->getValue($entity);
            if ($value === null) {
                continue;
            }
            $key = $prop->getName();
            if ($changeCase === true) {
                $key = Utils::camelCaseToSnakeCase($key);
            }
            if ($value instanceof \DateTime) {
                $value = $value->format(EntityBuilder::DATETIME_FORMAT);
            }

            if ($value instanceof Entity) {
                $value = $value->getId();
                $key = "${$key}_id";
            }

            $entityData[$key] = $value;
        }

        return $entityData;
    }

    /**
     * @param $entity
     *
     * @return \ReflectionProperty[]
     */
    protected static function getProperties($entity)
    {
        $class = $entity;
        if (is_object($entity)) {
            $class = get_class($entity);
        }

        if (!isset(self::$entitiesProperties[$class])) {
            $reflectionClass = new \ReflectionClass($class);
            $parentClass = $reflectionClass->getParentClass();
            $parentProps = [];
            if ($parentClass) {
                $parentProps = self::getProperties($parentClass->getName());
            }
            self::$entitiesProperties[$class] = array_merge(
                $reflectionClass->getProperties(),
                $parentProps
            );

        }

        return self::$entitiesProperties[$class];
    }
}