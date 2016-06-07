<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsTotalCoredeVoteRepository extends EntityRepository
{

    public function findTotalVotes()
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT v.ballot_box_id, c.id AS corede_id, c.name AS corede_name,
                    COUNT(CASE WHEN v.voter_registration IS NOT NULL AND v.login_cidadao_id IS NULL THEN 1 ELSE NULL END) AS total_with_voter_registration,
                    COUNT(CASE WHEN v.login_cidadao_id IS NOT NULL AND v.voter_registration IS NULL THEN 1 ELSE NULL END) AS total_with_login_cidadao,
                    COUNT(CASE WHEN v.voter_registration IS NOT NULL AND v.login_cidadao_id IS NOT NULL THEN 1 ELSE NULL END) AS total_with_voter_registration_and_login_cidadao,
                    COUNT(*) AS total_votes
               FROM vote v
         INNER JOIN corede c
                 ON v.corede_id = c.id
           GROUP BY v.ballot_box_id, c.id, c.name
        ');

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findOneByCoredeId(Poll $poll, $coredeId)
    {
        return $this->createQueryBuilder('s')
                ->join('PROCERGSVPRCoreBundle:BallotBox', 'b', 'WITH',
                    'b.id = s.ballotBoxId')
                ->where('s.coredeId = :coredeId')
                ->andWhere('b.poll = :poll')
                ->setParameters(compact('poll', 'coredeId'))
                ->getQuery()->getOneOrNullResult();
    }

    public function findTotalVotesByPoll($poll)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT
  COUNT(CASE WHEN b.is_online IS TRUE THEN 1 END) AS votes_online,
        COUNT(CASE WHEN b.is_online IS FALSE THEN 1 END) AS votes_offline,
        ov.corede_id,
        c.name
   FROM ballot_box b
   INNER JOIN open_vote ov ON ov.ballot_box_id = b.id
   INNER JOIN corede c on c.id = ov.corede_id
   WHERE b.poll_id = :poll
   AND ov.poll_option_id IS NOT NULL
   GROUP BY ov.corede_id, c.name
   ORDER BY c.name

        ');

        $statement->bindParam('poll', $poll);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalVotersByPoll($poll)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT
  COUNT(CASE WHEN b.is_online IS TRUE THEN 1 END) AS voters_online,
        COUNT(CASE WHEN b.is_online IS FALSE THEN 1 END) AS voters_offline,
        v.corede_id,
        c.name
   FROM ballot_box b
   INNER JOIN vote v ON v.ballot_box_id = b.id
   INNER JOIN corede c on c.id = v.corede_id
   WHERE b.poll_id = :poll
   AND v.is_invalid IS NOT TRUE
   GROUP BY v.corede_id, c.name
   ORDER BY c.name

        ');

        $statement->bindParam('poll', $poll);
        $statement->execute();

        return $statement->fetchAll();
    }
}
