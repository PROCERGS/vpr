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
            ->join(
                'PROCERGSVPRCoreBundle:Step',
                's',
                'WITH',
                'o.step = s'
            )
            ->where('s.poll = :poll')
            ->andWhere('o.corede = :corede')
            ->addOrderBy('s.sorting', 'ASC')
            ->addOrderBy('o.categorySorting', 'ASC');
    }

    public function checkStepOptions(Step $step, $options)
    {
        if (count($options) > $step->getMaxSelection()) {
            return false;
        }
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

    /**
     * @param Vote $vote
     * @return array
     */
    public function getPollOption($vote)
    {
        $ids = $vote->getPollOption();
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('o')
            ->from('PROCERGSVPRCoreBundle:PollOption', 'o')
            ->where('o.id in (:ids)');

        return $query->getQuery()->setParameters(compact('ids'))->getResult();
    }

    /**
     * @param array $options
     * @param Poll $poll
     * @param Corede $corede
     * @return array
     */
    public function findByCategorySorting($options, Poll $poll, Corede $corede)
    {
        if (!is_array($options)) {
            $options = [$options];
        }

        $query = $this->createQueryBuilder('o')
            ->select('o')
            ->innerJoin('PROCERGSVPRCoreBundle:Step', 's', 'WITH', 's = o.step')
            ->where('o.corede = :corede')
            ->andWhere('o.categorySorting IN (:options)')
            ->andWhere('s.poll = :poll')
            ->setParameters(compact('corede', 'options', 'poll'));

        return $query->getQuery()->getResult();
    }

    public function findLastPollOption()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT p FROM PROCERGSVPRCoreBundle:PollOption p ORDER BY p.id DESC'
                        )->setMaxResults(1)->getOneOrNullResult();
    }

}
