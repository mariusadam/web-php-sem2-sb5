<?php

namespace WebApp\Repository;

use WebApp\Services\EntityMapper;
use Doctrine\DBAL\Connection;
use Symfony\Component\Config\Definition\Exception\Exception;
use WebApp\Services\EntityBuilder;


/**
 * Class AbstractRepository
 *
 * @package App\Repository
 *
 * @author  Marius Adam
 */
abstract class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EntityBuilder
     */
    protected $entityBuilder;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * AbstractRepository constructor.
     *
     * @param Connection    $connection
     * @param EntityBuilder $entityBuilder
     * @param               $entityClass
     */
    public function __construct(Connection $connection, EntityBuilder $entityBuilder, $entityClass)
    {
        $this->connection = $connection;
        $this->entityBuilder = $entityBuilder;
        $this->entityClass = $entityClass;
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function findBy($id)
    {
        $qb = $this->connection->createQueryBuilder();
        $stmt = $qb->select('*')
                   ->from($this->getEntityTable(), 't')
                   ->where('t.id = :id')
                   ->setParameter('id', $id)
                   ->execute();

        $rows = $stmt->fetchAll();
        if (count($rows) != 1) {
            $e = new $this->entityClass();
            throw new \Exception(
                "Could not find one {$e} with id {$id}."
            );
        }

        return $this->entityBuilder->build($rows[0], $this->entityClass);
    }

    /**
     * @param $entity
     */
    public function save($entity)
    {
        $entityData = EntityMapper::toArray($entity);
        if ($this->isUpdate($entityData)) {
            $this->update($entityData);
        } else {
            $this->insert($entityData);
        }
    }

    /**
     * @param mixed $entity Entity object or it's id
     *
     * @return int
     */
    public function delete($entity)
    {
        if (is_numeric($entity)) {
            $id = $entity;
        } else {
            $id = $entity->{'getId'}();
        }

        $affectedRows = $this->connection->delete(
            $this->getEntityTable(),
            ['id' => $id]
        );

        if ($affectedRows < 1) {
            $e = new $this->entityClass();
            throw new Exception("Could not delete {$e} with id {$id}");
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return array_map(
            [$this, 'toEntity'],
            $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from($this->getEntityTable())
                ->execute()
                ->fetchAll()
        );
    }

    /**
     * @return int[]
     */
    public function getAllIds()
    {
        $ids = $this->connection
            ->createQueryBuilder()
            ->select('t.id')
            ->from($this->getEntityTable(), 't')
            ->execute()
            ->fetchAll();
        $ids = array_column($ids, 'id');

        return array_combine($ids, $ids);
    }

    protected function toEntity($row)
    {
        if (empty($row)) {
            return null;
        }
        return $this->entityBuilder->build($row, $this->entityClass);

    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function isUpdate(array $data)
    {
        return array_key_exists('id', $data);
    }

    /**
     * @param array $data
     *
     * @return int
     */
    protected function update(array $data)
    {
        $id = $data['id'];
        unset($data['id']);

        return $this->connection->update(
            $this->getEntityTable(),
            $data,
            ['id' => $id]
        );
    }

    /**
     * @param array $data
     *
     * @return int
     */
    protected function insert(array $data)
    {
        return $this->connection->insert(
            $this->getEntityTable(),
            $data
        );
    }

    /**
     * @return string
     */
    protected abstract function getEntityTable();
}
