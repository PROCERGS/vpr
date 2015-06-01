<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
}
