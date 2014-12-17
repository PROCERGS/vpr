<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsTotalOptionVoteRepository extends EntityRepository
{
    public function findTotalOptionVoteByCorede($corede)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s.coredeId, t.id as stepId, o.id AS optionId, o.categorySorting AS optionNumber, o.title AS optionTitle, COUNT(s.id) AS totalVotes')
            ->from('PROCERGSVPRCoreBundle:StatsOptionVote', 's')
            ->join('PROCERGSVPRCoreBundle:PollOption', 'o', 'WITH','s.pollOptionId = o.id')
            ->join('PROCERGSVPRCoreBundle:Step', 't', 'WITH','t = o.step')
            ->where('s.coredeId = :corede')
            ->setParameter('corede', $corede)
            ->groupBy('s.coredeId, t.id, o.id, o.categorySorting, o.title');

         return $query->getQuery()->getResult();
    }

    public function findPercentOptionVoteByCoredeAndStep($corede_id, $step_id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
            SELECT s.option_number, s.option_title, s.total_votes, (s.total_votes / t.total ) * 100 AS percent, s.created_at
              FROM stats_total_option_vote s
        INNER JOIN (
                       SELECT SUM(total_votes) AS total, s.corede_id, s.option_step_id
                         FROM stats_total_option_vote s
                        WHERE s.corede_id = :corede_id
                          AND s.option_step_id = :step_id
                     GROUP BY s.option_step_id,s.corede_id
                   ) AS t
                ON t.corede_id = s.corede_id
               AND t.option_step_id =  s.option_step_id
          ORDER BY s.total_votes DESC
        ');

        $statement->bindParam('corede_id', $corede_id);
        $statement->bindParam('step_id', $step_id);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalOptionVoteByCoredeAndStep($corede_id, $step_id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
            SELECT s.option_number, s.option_title, s.total_votes AS total, s.created_at
              FROM stats_total_option_vote s
        INNER JOIN (
                       SELECT SUM(total_votes) AS total, s.corede_id, s.option_step_id
                         FROM stats_total_option_vote s
                        WHERE s.corede_id = :corede_id
                          AND s.option_step_id = :step_id
                     GROUP BY s.option_step_id,s.corede_id
                   ) AS t
                ON t.corede_id = s.corede_id
               AND t.option_step_id =  s.option_step_id
          ORDER BY s.total_votes DESC
        ');

        $statement->bindParam('corede_id', $corede_id);
        $statement->bindParam('step_id', $step_id);
        $statement->execute();

        return $statement->fetchAll();
    }
}