<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StepRepository extends EntityRepository
{

    public function findNextPollStep($vote)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from('PROCERGSVPRCoreBundle:Step', 's')
            ->where('s.poll = :poll')
            ->addOrderBy('s.sorting', 'ASC');

        $parameters = array('poll' => $vote->getBallotBox()->getPoll());
        if ($vote->getLastStep()) {
            $query->andWhere('s.sorting > :sorting');
            $parameters['sorting'] = $vote->getLastStep()->getSorting();
        }
        return $query->getQuery()
                ->setParameters($parameters)
                ->setMaxResults(1)
                ->getOneOrNullResult();
    }
}
