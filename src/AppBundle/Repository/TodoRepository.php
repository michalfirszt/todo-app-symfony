<?php

namespace AppBundle\Repository;

/**
 * TodoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TodoRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOverview()
    {
        $sql = "SELECT t.id AS id, 
                        t.name AS name, 
                        t.description AS description, 
                        t.category AS category 
                        FROM AppBundle:Todo t";

        $result = $this->getEntityManager()
                        ->createQuery($sql)
                        ->getResult();

        return $result;
    }

    public function findNumberOfTasks()
    {
        $sql = "SELECT count(t.id) AS totalNumber
                        FROM AppBundle:Todo t";

        $result = $this->getEntityManager()
                        ->createQuery($sql)
                        ->getResult();

        return $result;
    }
}
