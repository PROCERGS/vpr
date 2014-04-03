<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PollOptionRepository extends EntityRepository
{

    public function findByPollCoredeStep($poll, $corede, $step)
    {
        return $this->getEntityManager()->getRepository('PROCERGS\VPR\CoreBundle\Entity\PollOption')
                        ->findBy(compact('poll', 'corede', 'step'));
    }

}
