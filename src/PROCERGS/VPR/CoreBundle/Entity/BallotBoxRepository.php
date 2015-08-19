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
        $lastPin = str_pad(9, $length, 9);
        $allPins = range(1, $lastPin);

        $query = $this->createQueryBuilder('b')
            ->select('b.pin')
            ->where('b.poll = :poll')
            ->setParameter('poll', $poll);

        $result = $query->getQuery()->getScalarResult();
        $pins   = array_map('current', $result);

        $available = array_diff($allPins, $pins);
        $pin       = $available[rand(0, count($available) - 1)];
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

    public function getActivationStatistics(Poll $poll)
    {
        $isOnline = false;
        return $this->createQueryBuilder('b')
                ->select('b.id, b.pin, b.setupAt, b.closedAt')
                ->where('b.poll = :poll')
                ->andWhere('b.isOnline = :isOnline')
                ->setParameters(compact('poll', 'isOnline'))
                ->getQuery()->getScalarResult();
    }
}
