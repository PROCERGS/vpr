<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    public function findByPoll($poll)
    {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT v FROM PROCERGSVPRCoreBundle:Vote v INNER JOIN PROCERGSVPRCoreBundle:BallotBox b WHERE b.poll = :poll'
                        )->setParameter('poll', $poll)->getResult();
    }

}
