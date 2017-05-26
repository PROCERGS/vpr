<?php
namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RlCriterioMunRepository extends EntityRepository
{

    public function findEspecial1($id, $typeCalc)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
select a1.*
from rl_criterio_mun a1
where poll_id = :poll_id and a1.type_calc = :type_calc

order by a1.limit_citizen asc
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('poll_id', $id);
        $stmt->bindParam('type_calc', $typeCalc);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function saveComplete($pollId, &$item1, &$item2)
    {

        function n(&$val)
        {
            if ("" === $val || null === $val) {
                return null;
            }
            
            if (is_numeric($val)) {
                return $val;
            } else {
                return str_replace(array(
                    '.',
                    ','
                ), array(
                    '',
                    '.'
                ), $val);
            }
        }
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        try {
            $conn->beginTransaction();
            $sql = 'delete from rl_criterio_mun where poll_id = :poll_id';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam('poll_id', $pollId);
            $a = $stmt->execute();
            
            $sql = 'insert into rl_criterio_mun (poll_id, type_calc, limit_citizen, perc_apply) values (?,?,?,?)';
            $stmt = $conn->prepare($sql);
            if ($item1) {
                foreach ($item1 as $key => $val) {
                    foreach ($val as $key1 => $val2) {
                        if (! is_numeric(n($val2['perc_apply']))) {
                            continue;
                        }
                        $c = array(
                            n($pollId),
                            n($key1),
                            n($val2['limit_citizen']),
                            n($val2['perc_apply'])
                        );
                        $a = $stmt->execute($c);
                    }
                }
            }
            if ($item2) {
                foreach ($item2 as $key => $val) {
                    foreach ($val as $key1 => $val2) {
                        if (! is_numeric(n($val2['perc_apply']))) {
                            continue;
                        }
                        $c = array(
                            n($pollId),
                            n($key1),
                            n($val2['limit_citizen']),
                            n($val2['perc_apply'])
                        );
                        $a = $stmt->execute($c);
                    }
                }
            }
            
            $conn->commit();
        } catch (\Exception $e) {            
            $conn->rollBack();
            throw $e;
        }
        return true;
    }
}
