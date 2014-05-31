<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsOptionVoteRepository extends EntityRepository
{
    public function findOptionVoteByCorede($corede, $step)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(s.id) as value, o.title, o.categorySorting as number')
            ->from('PROCERGSVPRCoreBundle:StatsOptionVote', 's')
            ->join('PROCERGSVPRCoreBundle:PollOption', 'o', 'WITH','s.pollOptionId = o.id')
            ->where('s.coredeId = :corede')
            ->andWhere('o.step = :step')
            ->setParameter('corede', $corede)
            ->setParameter('step', $step)
            ->orderBy('value','desc')
            ->groupBy('o.title, o.categorySorting');

         return $query->getQuery()->getResult();
    }

    public function findPercentOptionVoteByCorede($corede, $step)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
                SELECT (COUNT(*) / t.total_step) * 100 AS value, t.total_step, o.category_sorting as number, o.title, o.step_id, o.corede_id
                  FROM stats_option_vote s 
            INNER JOIN poll_option o
                    ON s.poll_option_id = o.id
            INNER JOIN (
                            SELECT COUNT(*) AS total_step, s.corede_id, o.step_id 
                              FROM stats_option_vote s 
                        INNER JOIN poll_option o
                                ON s.poll_option_id = o.id
                             WHERE s.corede_id = :corede_id
                               AND o.step_id = :step_id) AS t 
                    ON t.corede_id = o.corede_id AND t.step_id =  o.step_id
                 WHERE s.corede_id = :corede_id
                   AND o.step_id = :step_id
              GROUP BY o.title, o.category_sorting, o.corede_id, o.step_id
              ORDER BY value DESC
        ');

        $statement->bindValue('corede_id', $corede);
        $statement->bindValue('step_id', $step);
        $statement->execute();

        return $statement->fetchAll();
    }    

    public function findTotalVotes()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
                SELECT COUNT(*) AS total,  c.name, COUNT(CASE WHEN v.voter_registration IS NOT NULL THEN 1 ELSE NULL END) AS total_voter_registration
                  FROM vote v
            INNER JOIN corede c
                    ON v.corede_id = c.id
              GROUP BY c.name
              ORDER BY total DESC
        ');

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalVotesByCorede()
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(v.id) as total, c.name')
            ->from('PROCERGSVPRCoreBundle:Vote', 'v')
            ->join('PROCERGSVPRCoreBundle:Corede', 'c', 'WITH','v.corede = c')
            ->orderBy('total','desc')
            ->groupBy('c.name');

         return $query->getQuery()->getResult();
    }    
}
