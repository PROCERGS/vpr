<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PollRepository extends EntityRepository
{

    public function findActivePoll()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT p FROM PROCERGSVPRCoreBundle:Poll p ORDER BY p.openingTime DESC'
                        )->setMaxResults(1)->getResult();
    }

}
