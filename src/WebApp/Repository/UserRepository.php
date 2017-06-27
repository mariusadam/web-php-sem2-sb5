<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/27/17
 * Time: 8:01 AM
 */

namespace WebApp\Repository;


use Doctrine\DBAL\Connection;
use WebApp\Entity\User;
use WebApp\Services\EntityBuilder;

class UserRepository extends AbstractRepository
{
    public function __construct(Connection $connection, EntityBuilder $entityBuilder)
    {
        parent::__construct($connection, $entityBuilder, User::class);
    }

    /**
     * @param string $username
     * @param string $password
     * @return User
     * @throws \Exception
     */
    public function findByUsernameAndPassword($username, $password)
    {
        $qb = $this->connection->createQueryBuilder();
        $stmt = $this->connection->executeQuery(
            "SELECT * FROM users t WHERE t.password = ? and t.username = '$username'",
            [$password]
        );

        $rows = $stmt->fetchAll();
        if (count($rows) < 1) {
            throw new \Exception(
                "Invalid username or password."
            );
        }

        return $this->entityBuilder->build($rows[0], $this->entityClass);
    }

    /**
     * @return string
     */
    protected function getEntityTable()
    {
        return 'users';
    }
}