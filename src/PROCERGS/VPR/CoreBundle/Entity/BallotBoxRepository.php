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

    public function findActiveOnline()
    {
        $now      = new \DateTime();
        $isOnline = true;
        return $this->createQueryBuilder('b')
                ->innerJoin('b.poll', 'p')
                ->where(':now BETWEEN p.openingTime AND p.closingTime')
                ->andWhere('b.isOnline = :isOnline')
                ->setParameters(compact('now', 'isOnline'))
                ->getQuery()->getOneOrNullResult();
    }

    public function generateUniquePin(Poll $poll, $length = 6)
    {
        $query = $this->createQueryBuilder('b')
            ->select('b.pin')
            ->where('b.poll = :poll')
            ->setParameter('poll', $poll);

        $result = $query->getQuery()->getScalarResult();
        $pins   = array_map('current', $result);

        do {
            $r   = rand(0, str_repeat('9', $length));
            $pin = str_pad($r, $length, 0, STR_PAD_LEFT);
        } while (array_search($pin, $pins) !== false);

        return $pin;
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
}
