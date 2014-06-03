<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatsOptionVoteRepository extends EntityRepository
{
    public function findOptionVoteByCorede($corede, $step)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('count(s.id) as value, o.title, o.categorySorting as number')
            ->from('PROCERGSVPRCoreBundle:StatsOptionVote', 's')
            ->join('PROCERGSVPRCoreBundle:PollOption', 'o', 'WITH','s.pollOptionId = o.id')
            ->where('s.coredeId = :corede')
            ->andWhere('o.step = :step')
            ->setParameter('corede', $corede)
            ->setParameter('step', $step)
            ->orderBy('value','desc')
            ->groupBy('o.title, o.categorySorting');

         return $query->getQuery()->getResult();
    } 
    
}
