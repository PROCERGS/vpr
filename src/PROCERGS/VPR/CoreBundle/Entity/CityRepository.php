<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{

    public function findByCityName($cityNames)
    {
        $lowerNames = array_map(function ($value) {
            return strtolower($value);
        }, $cityNames);

        $cities = $this->createQueryBuilder('c')
                ->where('LOWER(c.name) IN (:names)')
                ->setParameter('names', $lowerNames)
                ->getQuery()->getResult();

        $result = array();
        foreach ($cities as $city) {
            $result[strtolower($city->getName())] = $city;
        }

        return $result;
    }
}
