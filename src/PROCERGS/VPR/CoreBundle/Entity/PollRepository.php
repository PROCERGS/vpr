<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PollRepository extends EntityRepository
{

    public function findActivePoll()
    {
        $now = new \DateTime();
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT p FROM PROCERGSVPRCoreBundle:Poll p WHERE :now BETWEEN p.openingTime AND p.closingTime ORDER BY p.openingTime DESC'
                        )->setParameters(compact('now'))->setMaxResults(1)->getOneOrNullResult();
    }

}
