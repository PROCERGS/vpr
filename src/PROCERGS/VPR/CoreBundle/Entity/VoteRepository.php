<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    public function findByPoll($poll)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT v FROM PROCERGSVPRCoreBundle:Vote v JOIN PROCERGSVPRCoreBundle:BallotBox b WITH v.ballotBox = b WHERE b.poll = :poll'
            )->setParameter('poll', $poll)->getResult();
    }

    public function findByEspecial($filter)
    {
        $sql = 'SELECT v FROM PROCERGSVPRCoreBundle:Vote v WHERE v.ballotBox = :ballotBox ';
        if (isset($filter['voterRegistration'])) {
            $sql .= 'and v.voterRegistration = :voterRegistration ';
        }
        if (isset($filter['loginCidadaoId'])) {
            $sql .= 'and v.loginCidadaoId = :loginCidadaoId ';
        }
        if (isset($filter['vote'])) {
            $sql .= 'and v != :vote ';
        }

        return $this->getEntityManager()->createQuery($sql)->setParameters($filter)->getResult();
    }

    public function getVotesPerMinute(Poll $poll, BallotBox $ballotBox = null)
    {
        $query = $this->createQueryBuilder('v')
            ->select(
                "YEAR(v.createdAt) AS year, MONTH(v.createdAt) AS month, DAY(v.createdAt) AS day, HOUR(v.createdAt) AS hour, MINUTE(v.createdAt) AS minute, COUNT(v) AS votes"
            )
            ->join('v.ballotBox', 'b')
            ->where('b.poll = :poll')
            ->groupBy('year, month, day, hour, minute')
            ->setParameter('poll', $poll);

        if ($ballotBox instanceof BallotBox) {
            $query->andWhere('b = :ballotbox')
                ->setParameter('ballotbox', $ballotBox);
        }

        return $query->getQuery()->getScalarResult();
    }

    public function getOfflineIds(Poll $poll)
    {
        $offlineIds = $this->createQueryBuilder('v')
            ->select('v.offlineId')
            ->join('v.ballotBox', 'b')
            ->where('b.poll = :poll')
            ->andWhere('v.offlineId IS NOT NULL')
            ->setParameter('poll', $poll)
            ->getQuery()->getScalarResult();

        return array_map('reset', $offlineIds);
    }

    public function getVotesPerIp(
        Poll $poll,
        Corede $corede = null,
        City $city = null,
        $thresholdMin = 0,
        $thresholdMax = 0
    ) {
        $query = $this->createQueryBuilder('v')
            ->select('v.ipAddress, c.name AS city, c.id AS city_id, co.name AS corede, COUNT(v) AS votes')
            ->join('v.ballotBox', 'b')
            ->join('v.city', 'c')
            ->join('v.corede', 'co')
            ->where('b.poll = :poll')
            ->groupBy('v.ipAddress, c.name, c.id, co.name')
            ->orderBy('votes', 'DESC')
            ->setParameter('poll', $poll);

        if ($corede instanceof Corede) {
            $query->andWhere('c.corede = :corede')
                ->setParameter('corede', $corede);
        }
        if ($city instanceof City) {
            $query->andWhere('b.city = :city')
                ->setParameter('city', $city);
        }

        if ($thresholdMin > 0) {
            $query->andHaving('COUNT(v) >= :min')
                ->setParameter('min', $thresholdMin);
        }

        if ($thresholdMax > 0) {
            $query->andHaving('COUNT(v) <= :max')
                ->setParameter('max', $thresholdMax);
        }

        return $query->getQuery()->getScalarResult();
    }
}
