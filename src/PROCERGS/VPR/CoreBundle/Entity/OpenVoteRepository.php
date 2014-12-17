<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OpenVoteRepository extends EntityRepository
{
    public function findOptionVoteByCityAndStep($city_id, $step_id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
            SELECT po.id, po.category_sorting as option_number, po.title as option_title, count(ov.poll_option_id) as total
              FROM poll_option po
        INNER JOIN open_vote ov
                ON po.id = ov.poll_option_id
             WHERE po.step_id = :step_id
               AND ov.city_id = :city_id
          GROUP BY ov.poll_option_id, po.id, po.category_sorting, po.title
          ORDER BY total DESC
        ');

        $statement->bindParam('city_id', $city_id);
        $statement->bindParam('step_id', $step_id);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalByCity($city_id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
            SELECT count(*) as total
              FROM vote v
             WHERE v.city_id = :city_id
        ');

        $statement->bindParam('city_id', $city_id);
        $statement->execute();

        return $statement->fetch();
    }
}
