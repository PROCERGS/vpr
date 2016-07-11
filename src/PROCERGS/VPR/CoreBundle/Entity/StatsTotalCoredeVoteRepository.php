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
  sum(b.tot_votes_online) AS votes_online,
        sum(b.tot_votes_offline) AS votes_offline,
            sum(b.tot_votes_sms) AS votes_sms,
        b.corede_id,
        c.name
   FROM stats_prev_ppp b
   INNER JOIN corede c on c.id = b.corede_id
   WHERE b.poll_id = :poll
   GROUP BY b.corede_id, c.name
   ORDER BY c.name

        ');

        $statement->bindParam('poll', $poll);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalVotesByPollAndCorede($poll, $corede)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT
  sum(b.tot_votes_online) AS votes_online,
        sum(b.tot_votes_offline) AS votes_offline,
        sum(b.tot_votes_sms) AS votes_sms,
        b.city_id,
        city.name
   FROM stats_prev_ppp b
   INNER JOIN corede c on c.id = :corede
   INNER JOIN city on city.id = b.city_id
   WHERE b.poll_id = :poll
   AND b.corede_id = :corede
   GROUP BY b.city_id, city.name
   ORDER BY city.name
        ');

        $statement->bindParam('poll', $poll);
        $statement->bindParam('corede', $corede);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findTotalVotersByPoll($poll)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT
  sum(b.tot_voters_online ) AS voters_online,
        sum(b.tot_voters_offline) AS voters_offline,
            sum(b.tot_voters_sms) AS voters_sms,
        b.corede_id,
        c.name
   FROM stats_prev_ppp b
   INNER JOIN corede c on c.id = b.corede_id
   WHERE b.poll_id = :poll
   GROUP BY b.corede_id, c.name
   ORDER BY c.name
        ');

        $statement->bindParam('poll', $poll);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public function findTotalVotersByPollFake($poll)
    {
        $em = $this->getEntityManager();
        /**
         * @var \Doctrine\DBAL\Connection $connection
         */
        $connection = $em->getConnection();
        $sql = '
with tb1 as (
select a1.corede_id
, count(case when a1.voter_registration is not null and a2.is_online = true then 1 else null end) voters_online
, count(case when a1.voter_registration is not null and a2.is_online = false and a2.is_sms = true then 1 else null end) voters_sms
, count(case when a1.voter_registration is not null and a2.is_online = false and a2.csv is not null then 1 else null end) voters_offline
from vote a1 
inner join ballot_box a2
on a1.ballot_box_id = a2.id
where a2.poll_id = ?
group by a1.corede_id            
), tb2 as (
select a2.id, a2.name, sum(a1.tot_voter) tot_pop
from city a1
inner join corede a2 on a1.corede_id = a2.id
group by a2.id, a2.name
)
select *
from tb2 
left join tb1 on tb1.corede_id = tb2.id
order by tb2.name
        ';
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll));
        $stmt1->setFetchMode(\PDO::FETCH_ASSOC);
        while ($vote = $stmt1->fetch()) {
            $coredeId = $vote['corede_id'];
            $t = array();
            $t['corede_id'] = $coredeId;
            $t['corede'] = $vote['name'];
            $t['voters_online'] = $vote['voters_online'];
            $t['voters_offline'] = $vote['voters_offline'];
            $t['voters_sms'] = $vote['voters_sms'];
            $t['tot_pop'] = $vote['tot_pop'];
            $t['tot'] = $vote['voters_online'] + $vote['voters_offline'] + $vote['voters_sms'];
            $t['perc'] = number_format(($t['tot'] * 100) /$t['tot_pop'], 2);
            $coredes[$coredeId] = $t;
        }
        return $coredes;
    }    

    public function findTotalVotersByPollAndCorede($poll, $corede)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare('
             SELECT
  sum(b.tot_voters_online ) AS voters_online,
        sum(b.tot_voters_offline) AS voters_offline,
            sum(b.tot_voters_sms) AS voters_sms,
        b.city_id,
        city.name
   FROM stats_prev_ppp b
   INNER JOIN corede c on c.id = :corede
   INNER JOIN city on city.id = b.city_id
   WHERE b.poll_id = :poll
   AND b.corede_id = :corede
   GROUP BY b.city_id, city.name
   ORDER BY city.name
        ');

        $statement->bindParam('poll', $poll);
        $statement->bindParam('corede', $corede);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public function findTotalVotersByPollAndCoredeFake($poll, $corede)
    {
        $em         = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = "
with tb1 as (
select a1.city_id
, count(case when a1.voter_registration is not null and a2.is_online = true then 1 else null end) voters_online
, count(case when a1.voter_registration is not null and a2.is_online = false and a2.is_sms = true then 1 else null end) voters_sms
, count(case when a1.voter_registration is not null and a2.is_online = false and a2.csv is not null then 1 else null end) voters_offline
from vote a1
inner join ballot_box a2
on a1.ballot_box_id = a2.id
where a2.poll_id = :poll_id and a1.corede_id = :corede_id
group by a1.city_id
), tb2 as (
select a1.id, a1.name, sum(a1.tot_voter) tot_pop
from city a1
where corede_id = :corede_id
group by a1.id, a1.name
)
select *
from tb2 
left join tb1 on tb1.city_id = tb2.id
order by tb2.name
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array('poll_id' => $poll, 'corede_id' => $corede));
        $stmt1->setFetchMode(\PDO::FETCH_ASSOC);
        while ($vote = $stmt1->fetch()) {
            $tId = $vote['city_id'];
            $t = array();
            $t['city_id'] = $tId;
            $t['city'] = $vote['name'];
            $t['voters_online'] = $vote['voters_online'];
            $t['voters_offline'] = $vote['voters_offline'];
            $t['voters_sms'] = $vote['voters_sms'];
            $t['tot_pop'] = $vote['tot_pop'];
            $t['tot'] = $vote['voters_online'] + $vote['voters_offline'] + $vote['voters_sms'];
            $t['perc'] = number_format(($t['tot'] * 100) /$t['tot_pop'], 2);
            $cities[$tId] = $t;
        }
        return $cities;
    }
}
