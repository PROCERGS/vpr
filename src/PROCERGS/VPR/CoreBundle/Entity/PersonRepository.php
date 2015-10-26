<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PersonRepository extends EntityRepository
{

    /**
     * @param integer $limit
     * @return \Doctrine\ORM\Query
     */
    public function getPendingReminderQuery($limit = null)
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.email IS NOT NULL')
            ->andWhere('p.loginCidadaoAcceptRegistration = TRUE')
            ->andWhere('p.firstName IS NOT NULL')
            ->andWhere('p.loginCidadaoId IS NULL')
            ->andWhere('p.loginCidadaoSentReminder is null');

        if ($limit !== null) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery();
    }

    public function getPendingReminder($limit = null)
    {
        return $this->getPendingReminderQuery($limit)->getQuery()->getResult();
    }

    public function getfindLoginCidadaoQuery()
    {
        return $this->createQueryBuilder('p')
                ->where('p.loginCidadaoId IS NOT NULL');
    }
}
