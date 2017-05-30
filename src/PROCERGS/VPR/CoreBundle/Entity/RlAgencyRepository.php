<?php
namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RlAgencyRepository extends EntityRepository
{

    public function findEspecial1($id)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
select a1.*
from rl_agency a1
where poll_id = :poll_id
order by a1.name asc
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('poll_id', $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
