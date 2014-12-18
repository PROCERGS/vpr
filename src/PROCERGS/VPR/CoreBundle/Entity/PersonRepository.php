<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PersonRepository extends EntityRepository
{

    public function getPendingReminder($limit = null)
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.email IS NOT NULL')
            ->andWhere('p.loginCidadaoAcceptRegistration = TRUE')
            ->andWhere('p.firstName IS NOT NULL')
            ->andWhere('p.loginCidadaoId IS NULL');

        if ($limit !== null) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

}
