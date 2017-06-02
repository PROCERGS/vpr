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
    
    public function findCombo1($coredeId = null)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
select a1.id, a1.name, a1.corede_id
from city a1
where 1 = 1
            ';
        $param = array();
        if ($coredeId) {
            $param['corede_id'] = $coredeId;
            $sql .= "and a1.corede_id = :corede_id ";
        }
        $sql .= 'order by a1.name asc ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($param);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
}
