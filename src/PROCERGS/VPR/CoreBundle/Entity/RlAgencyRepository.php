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
    
    public function copiarItens($pollId, $targetPollId)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = 'insert into rl_agency(poll_id, name) select :target_poll_id, name from rl_agency where poll_id = :poll_id';
        $stmt = $conn->prepare($sql);        
        return $stmt->execute(array('poll_id' => $pollId, 'target_poll_id' => $targetPollId));
    }
}
