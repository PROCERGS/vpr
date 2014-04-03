<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PollOptionRepository extends EntityRepository
{

    public function findByPollCoredeStep($poll, $corede, $step)
    {
        return $this->getEntityManager()->getRepository('PROCERGS\VPR\CoreBundle\Entity\PollOption')
                        ->findBy(compact('poll', 'corede', 'step'),
                                array('categorySorting' => 'ASC'));
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
                        ->join('PROCERGSVPRCoreBundle:Step', 's', 'WITH', 'o.step = s')
                        ->where('o.poll = :poll')
                        ->andWhere('o.corede = :corede')
                        ->addOrderBy('s.sorting', 'ASC')
                        ->addOrderBy('o.categorySorting', 'ASC');
    }

}
