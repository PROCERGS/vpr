<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use PROCERGS\VPR\CoreBundle\Entity\Poll;

class BallotBoxRepository extends EntityRepository
{

    public function findOnlineByPoll($poll)
    {
        $isOnline = true;
        $sql      = 'SELECT b FROM PROCERGSVPRCoreBundle:BallotBox b WHERE b.poll = :poll AND b.isOnline = :isOnline';
        return $this->getEntityManager()
                ->createQuery($sql)
                ->setParameters(compact('poll', 'isOnline'))
                ->getOneOrNullResult();
    }

    public function generateUniquePin(Poll $poll, $length = 6)
    {
    	$connection = $this->getEntityManager()->getConnection();
        return current($connection->query("select nextval('ballot_box_pin_seq')")->fetchAll(\PDO::FETCH_COLUMN));;
    }

    public function findByPinAndPollFilteredByCorede(Poll $poll, $pin)
    {
        $query = $this->createQueryBuilder('b')
            ->select('b, c, p, s, o')
            ->join('b.city', 'c')
            ->join('b.poll', 'p')
            ->join('p.steps', 's')
            ->join('s.pollOptions', 'o')
            ->where('b.poll = :poll')
            ->andWhere('b.pin = :pin')
            ->andWhere('o.corede = c.corede')
            ->setParameters(compact('poll', 'pin'));

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getActivationStatistics(Poll $poll)
    {
        $isOnline = false;
        return $this->createQueryBuilder('b')
                ->select('b.id, b.pin, b.setupAt, b.closedAt, c.name AS city_name')
                ->join('b.city', 'c')
                ->where('b.poll = :poll')
                ->andWhere('b.isOnline = :isOnline')
                ->orderBy('b.closedAt, b.setupAt', 'DESC')
                ->setParameters(compact('poll', 'isOnline'))
                ->getQuery()->getScalarResult();
    }
    
    public function hasOnline(Poll $poll)
    {
    		return $this->createQueryBuilder('b')
    	->select('b')
    	->where('b.isOnline = t and b.poll = :poll')
    	->andWhere('b.isOnline = t')
    	->setParameters(array('poll' => $poll))
    	->getQuery()->getOneOrNullResult();
    }
    
}
