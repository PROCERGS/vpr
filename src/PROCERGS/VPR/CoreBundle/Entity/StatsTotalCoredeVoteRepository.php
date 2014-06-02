<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsTotalCoredeVoteRepository extends EntityRepository
{
    public function findTotalVotes()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT v.ballot_box_id, c.id AS corede_id, c.name AS corede_name, 
                    COUNT(CASE WHEN v.voter_registration IS NOT NULL THEN 1 ELSE NULL END) AS total_with_voter_registration, 
                    COUNT(CASE WHEN v.login_cidadao_id IS NOT NULL THEN 1 ELSE NULL END) AS total_with_login_cidadao,
                    COUNT(*) AS total_votes
               FROM vote v
         INNER JOIN corede c
                 ON v.corede_id = c.id
           GROUP BY v.ballot_box_id, c.id, c.name
        ');

        $statement->execute();

        return $statement->fetchAll();
    }
}
