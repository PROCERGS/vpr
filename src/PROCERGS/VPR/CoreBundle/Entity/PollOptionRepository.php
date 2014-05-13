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

    public function getNextPollStep($vote)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('s')
                ->from('PROCERGSVPRCoreBundle:Step', 's')
                ->where('s.poll = :poll')
                ->addOrderBy('s.sorting', 'ASC');
        
        $pars = array('poll' => $vote->getBallotBox()->getPoll());
        if ($vote->getLastStep()) {
            $query->andWhere('s.sorting > :sorting');
            $pars['sorting'] = $vote->getLastStep()->getSorting();
        }
        return $query->getQuery()
                        ->setParameters($pars)
                        ->setMaxResults(1)
                        ->getOneOrNullResult();
    }

    public function checkStepOptions($step, $options)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('count(s) total')
                ->from('PROCERGSVPRCoreBundle:PollOption', 's')
                ->where('s.step = :step')
                ->andWhere('s.id in (:ids)');
        $a = $query->getQuery()->setParameters(array('step' => $step, 'ids' => $options))
                ->setMaxResults(1)
                ->getOneOrNullResult();
        return isset($a['total']) && $a['total'] == count($options);
    }

    public function getPollOption($vote)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('s')
                ->from('PROCERGSVPRCoreBundle:PollOption', 's')
                ->where('s.id in (:ids)');
        return $query->getQuery()->setParameters(array('ids' => $vote->getPollOption()))->getResult();
    }

}
