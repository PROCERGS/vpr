<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    public function findByPoll($poll)
    {
        return $this->getEntityManager()
                        ->createQuery(
                                'SELECT v FROM PROCERGSVPRCoreBundle:Vote v JOIN PROCERGSVPRCoreBundle:BallotBox b WITH v.ballotBox = b WHERE b.poll = :poll'
                        )->setParameter('poll', $poll)->getResult();
    }
    
    public function findByEspecial($filter)
    {
        $sql = 'SELECT v FROM PROCERGSVPRCoreBundle:Vote v WHERE v.ballotBox = :ballotBox ';
        if (isset($filter['voterRegistration'])) {
            $sql .= 'and v.voterRegistration = :voterRegistration ';
        }
        if (isset($filter['loginCidadaoId'])) {
            $sql .= 'and v.loginCidadaoId = :loginCidadaoId ';
        }
        if (isset($filter['vote'])) {
            $sql .= 'and v != :vote ';
        }
        return $this->getEntityManager()->createQuery($sql)->setParameters($filter)->getResult();
    }

}
