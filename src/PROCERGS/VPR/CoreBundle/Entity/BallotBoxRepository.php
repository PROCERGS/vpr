<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BallotBoxRepository extends EntityRepository
{

    public function findOnlineByPoll($poll)
    {
        $isOnline = true;
        $sql = 'SELECT b FROM PROCERGSVPRCoreBundle:BallotBox b WHERE b.poll = :poll AND b.isOnline = :isOnline';
        return $this->getEntityManager()
                        ->createQuery($sql)
                        ->setParameters(compact('poll', 'isOnline'))
                        ->getOneOrNullResult();
    }

}
