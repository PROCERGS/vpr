<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PollOptionRepository extends EntityRepository
{

    public function findByPollCoredeStep($poll, $corede, $step)
    {
        return $this->getFindByPollCoredeQuery()
                        ->andWhere('s.sorting = :sorting')
                        ->getQuery()
                        ->setParameters(array('poll' => $poll, 'corede' => $corede, 'sorting' => $step->getSorting()))
                        ->getResult();
    }

    public function findByPollCorede($poll, $corede)
    {
        return $this->getFindByPollCoredeQuery()
                        ->getQuery()
                        ->setParameters(compact('poll', 'corede'))
                        ->getResult();
    }

    private function getFindByPollCoredeQuery()
    {
        return $this->getEntityManager()->createQueryBuilder()
                        ->select('o')
                        ->from('PROCERGSVPRCoreBundle:PollOption', 'o')
                        ->join('PROCERGSVPRCoreBundle:Step', 's', 'WITH',
                                'o.step = s')
                        ->where('s.poll = :poll')
                        ->andWhere('o.corede = :corede')
                        ->addOrderBy('s.sorting', 'ASC')
                        ->addOrderBy('o.categorySorting', 'ASC');
    }

    public function checkStepOptions($step, $options)
    {
        $count = $this->getEntityManager()->createQueryBuilder()
                ->select('count(o) total')
                ->from('PROCERGSVPRCoreBundle:PollOption', 'o')
                ->where('o.step = :step')
                ->andWhere('o.id in (:ids)')
                ->getQuery()->setParameters(array('step' => $step, 'ids' => $options))
                ->setMaxResults(1)
                ->getOneOrNullResult();
        return isset($count['total']) && $count['total'] == count($options);
    }

    public function getPollOption($vote)
    {
        $ids = $vote->getPollOption();
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('o')
                ->from('PROCERGSVPRCoreBundle:PollOption', 'o')
                ->where('o.id in (:ids)');
        return $query->getQuery()->setParameters(compact('ids'))->getResult();
    }

}
