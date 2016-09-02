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
    	$em = $this->getEntityManager();
    	$connection = $em->getConnection();
    	$sql = "
select a1.ip_address ipAddress, a2.name city, a1.city_id city_id, a3.name corede, a1.total votes
from stats_prev_ppp4 a1
INNER JOIN city a2 ON a1.city_id = a2.id
INNER JOIN corede a3 ON a1.corede_id = a3.id
where a1.poll_id = :poll_id ";
    	$params = array('poll_id' => $poll->getId());
    	if ($corede instanceof Corede) {
    		$sql .= "and a1.corede_id = :corede_id ";
    		$params['corede_id'] = $corede->getId();
    	}
    	if ($city instanceof City) {
    		$sql .= "and a1.city_id = :city_id ";
    		$params['city_id'] = $city->getId();
    	}
    	if ($thresholdMin > 0) {
    		$sql .= "and a1.total >= :total_min ";
    		$params['total_min'] = $thresholdMin;
    	}
    	if ($thresholdMax > 0) {
    		$sql .= "and a1.total <= :total_max ";
    		$params['total_max'] = $thresholdMax;
    	}
    	$sql .= "order by a1.total desc, a1.corede_id, a1.city_id ";
    	$stmt1 = $connection->prepare($sql);
    	$stmt1->execute($params);
    	$stmt1->setFetchMode(\PDO::FETCH_NUM);
    	$result = array();
    	while ($linha = $stmt1->fetch()) {
    		$result[] = array(    				
    				'ipAddress' => $linha[0],
					'city' => $linha[1],
    				'city_id' => $linha[2],
    				'corede' => $linha[3],
    				'votes' => $linha[4],
    		);
    	}
    	return $result;
    }
}
