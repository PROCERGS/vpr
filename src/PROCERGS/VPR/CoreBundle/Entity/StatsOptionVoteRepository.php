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
    
    public function query1(){
        $municipiosQuery =  "SELECT m.latitude, m.longitude, m.codcorede, m.corede, (SELECT SUM(mu.populacao) FROM PROCERGSVPRCoreBundle:Municipality mu WHERE mu.corede = m.corede) as populacao";
        $municipiosQuery .= " FROM PROCERGSVPRCoreBundle:Municipality m";
        $municipiosQuery .= " WHERE m.populacao in (SELECT MAX(im.populacao) FROM PROCERGSVPRCoreBundle:Municipality im GROUP BY im.corede)";
        $municipiosQuery .= " GROUP BY m.latitude, m.longitude, m.codcorede, m.corede";
        $municipios = $this->getEntityManager()->createQuery($municipiosQuery)->getResult();
        return $municipios;
    }    
}
