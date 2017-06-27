<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 6/27/17
 * Time: 8:56 AM
 */

namespace WebApp\Repository;


use Doctrine\DBAL\Connection;
use WebApp\Entity\Event;
use WebApp\Services\EntityBuilder;

class EventRepository extends AbstractRepository
{
    public function __construct(Connection $connection, EntityBuilder $entityBuilder)
    {
        parent::__construct($connection, $entityBuilder, Event::class);
    }

    /**
     * @return \DateTime[]
     */
    public function getDistinctDates()
    {
        $toDateTime = function ($v) {
            return \DateTime::createFromFormat('Y-m-d', $v['DateOnly']);
        };

        //SELECT DATE(t.date) DateOnly FROM events t GROUP BY DateOnly
        return array_map(
            $toDateTime,
            $this->connection
                ->createQueryBuilder()
                ->select('DATE(t.date) as DateOnly')
                ->from($this->getEntityTable(), 't')
                ->groupBy('DateOnly')
                ->execute()
                ->fetchAll()
        );
    }

    /**
     * @return string
     */
    protected function getEntityTable()
    {
        return 'events';
    }
}