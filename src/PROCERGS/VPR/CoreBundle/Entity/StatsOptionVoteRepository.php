<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsOptionVoteRepository extends EntityRepository
{
    public function findOptionVoteByCorede($corede, $step)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(s.id) as total, o.title, o.categorySorting as number')
            ->from('PROCERGSVPRCoreBundle:StatsOptionVote', 's')
            ->join('PROCERGSVPRCoreBundle:PollOption', 'o', 'WITH','s.pollOptionId = o.id')
            ->where('s.coredeId = :corede')
            ->andWhere('o.step = :step')
            ->setParameter('corede', $corede)
            ->setParameter('step', $step)
            ->orderBy('total','desc')
            ->groupBy('o.title, o.categorySorting');

         return $query->getQuery()->getResult();
    }
    
    public function getTotalOptionVoteByCorede($corede, $step)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(s) as total')
            ->from('PROCERGSVPRCoreBundle:StatsOptionVote', 's')
            ->join('PROCERGSVPRCoreBundle:PollOption', 'o', 'WITH','s.pollOptionId = o.id')
            ->where('s.coredeId = :corede')
            ->andWhere('o.step = :step')
            ->setParameter('corede', $corede)
            ->setParameter('step', $step);

         $result = $query->getQuery()
                        ->setMaxResults(1)
                        ->getOneOrNullResult();

         return $result['total'];
         
    }
    
    public function findOptionVoteByCorede2($corede, $step)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
                SELECT (COUNT(*) / t.total_step) * 100 AS percent, t.total_step, o.category_sorting as number, o.title, o.step_id, o.corede_id
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
              ORDER BY percent DESC
        ');

        $statement->bindValue('corede_id', $corede);
        $statement->bindValue('step_id', $step);
        $statement->execute();

        return $statement->fetchAll();
    }    
}
