<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RlCriterioRepository extends EntityRepository
{

    public function findEspecial1($id)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
select a1.id corede_id, a1.name corede_name, a2.tot_value, a2.tot_program, a2.program1, a2.program2, a2.program3, a2.program4
from corede a1
left join rl_criterio a2 on a2.corede_id = a1.id and a2.poll_id = :poll_id
order by a1.id asc
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('poll_id', $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function saveComplete($pollId, $items)
    {
        function n(&$val) {
        if ("" === $val || null === $val) {
            return null;
        }

        if (is_numeric($val)) {
            return $val;
        } else {
            return str_replace(array('.', ','), array('', '.'), $val);
        }
        }
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        try {
            $conn->beginTransaction();
            $sql = 'delete from rl_criterio where poll_id = :poll_id';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam('poll_id', $pollId);
            $stmt->execute();
            
            $sql = 'insert into rl_criterio (corede_id, poll_id, tot_value, tot_program, program1, program2, program3, program4) values (?,?,?,?,?,?,?,?)';
            $stmt = $conn->prepare($sql);
            foreach ($items as $key => $val) {                
                $stmt->execute(array($key, n($pollId), n($val['tot_value']), n($val['tot_program']), n($val['program1']), n($val['program2']), n($val['program3']), n($val['program4'])));
            }
            $conn->commit();            
        } catch (\Exception $e) {
            $conn->rollBack();
        }
        return true;
    }
}
