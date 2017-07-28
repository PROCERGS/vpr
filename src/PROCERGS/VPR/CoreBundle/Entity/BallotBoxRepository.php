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

    public function findByPinAndPollFilteredByCorede($pin)
    {
        $query = $this->createQueryBuilder('b')
            ->select('b, c, p, s, o')
            ->join('b.city', 'c')
            ->join('b.poll', 'p')
            ->join('p.steps', 's')
            ->join('s.pollOptions', 'o')
            ->where('b.pin = :pin')
            ->andWhere('o.corede = c.corede')
            ->setParameters(compact('pin'));

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
    	->where('b.isOnline = true')
        ->andWhere('b.poll = :poll')
    	->setParameters(array('poll' => $poll))
    	->getQuery()->getOneOrNullResult();
    }

    public function hasSms(Poll $poll)
    {
            return $this->createQueryBuilder('b')
        ->select('b')
        ->where('b.isSms = true')
        ->andWhere('b.poll = :poll')
        ->setParameters(array('poll' => $poll))
        ->getQuery()->getOneOrNullResult();
    }

    public function findLastBallotBox()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT b FROM PROCERGSVPRCoreBundle:BallotBox b ORDER BY b.id DESC'
                        )->setMaxResults(1)->getOneOrNullResult();
    }
    
    public function salva(BallotBox &$obj)
    {
        if (null === self::$_findEspecial2) {
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $sql = 'select a1.id from city a1 where lower_unaccent(name) = lower_unaccent(?) limit 1';
            self::$_findEspecial2 = $conn->prepare($sql);
        }
        self::$_findEspecial2->execute(array($cityName));
        return current(self::$_findEspecial2->fetchAll(\PDO::FETCH_CLASS, 'PROCERGS\VPR\CoreBundle\Entity\City'));
    }
    

}
