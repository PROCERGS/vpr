<?php
namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BallotBoxBatchRepository extends EntityRepository
{

    public function findEspecial1($id)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = "
select a1.id
, a1.csv_input_name
, a1.csv_input_name
, case a1.status1 when 0 then 'Aguardando' when 1 then 'Processado' when 2 then 'Processando' end status1_label
, a1.tot_ok
, a1.tot_fail
from ballot_box_batch a1
where poll_id = :poll_id
order by a1.id desc
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('poll_id', $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
}
