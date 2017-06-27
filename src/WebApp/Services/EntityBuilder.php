<?php

namespace WebApp\Services;


/**
 * Class EntityBuilder
 *
 * @package App\Services
 *
 * @author  Marius Adam
 */
class EntityBuilder
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @const
     */
    const ENTITY_NAMESPACE = 'WebApp\\Entity\\';

    /**
     * @param array  $data
     * @param string $class
     *
     * @return mixed
     */
    public function build(array $data, $class)
    {
        $entity = new $class();
        foreach ($data as $property => $value) {
            if (strpos($property, '_id') !== false) {
                $value = $this->build($value, $this->getEntityClass($property));
                $property = str_replace('_id', '', $property);
            }
            if (strpos($property, 'date') !== false) {
                $value = \DateTime::createFromFormat(self::DATETIME_FORMAT, $value);
            }
            $setter = 'set'.Utils::snakeToPascalCase($property);
            $entity->{$setter}($value);
        }

        return $entity;
    }

    private function getEntityClass($referencedName, $suffix = '_id')
    {
        $shortName = str_replace($suffix, '', $referencedName);

        return self::ENTITY_NAMESPACE.Utils::snakeToPascalCase($shortName);
    }

    /**
     * @param $value
     * @param $entityClass
     *
     * @return array
     */
    private function buildArray($value, $entityClass)
    {
        $result = [];
        $value = json_decode($value, true);
        foreach ($value as $v) {
            $result[] = new $entityClass($v);
        }

        return $result;
    }
}