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
    
    private static $_findEspecial2 = null;
    /**
     * @return City
     */
    public function findEspecial2($cityName)
    {
        if (null === self::$_findEspecial2) {
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $sql = 'select a1.id from city a1 where lower_unaccent(name) = lower_unaccent(?) limit 1';
            self::$_findEspecial2 = $conn->prepare($sql);
        }
        self::$_findEspecial2->execute(array($cityName));        
        return current(self::$_findEspecial2->fetchAll(\PDO::FETCH_CLASS, 'PROCERGS\VPR\CoreBundle\Entity\City'));
    }
    
    private static $_findEspecial3 = null;
    /**
     * @return City
     */
    public function findEspecial3($cityName)
    {
        if (null === self::$_findEspecial3) {
        self::$_findEspecial3 = $this->createQueryBuilder('c')
                ->where('Unaccent1(c.name) = Unaccent1(:name)')                
                ->getQuery();
            
        }
        self::$_findEspecial3->execute(array('name' => $cityName));
        return current(self::$_findEspecial3->getResult());
    }
    
    
}
