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
select a1.id corede_id, a1.name corede_name, a2.tot_value, a2.tot_program, a2.program1, a2.program2, a2.program3, a2.program4, a2.program5
from corede a1
left join rl_criterio a2 on a2.corede_id = a1.id and a2.poll_id = :poll_id
order by a1.id asc
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('poll_id', $id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findEspecial2($filter)
    {
        if (!isset($filter['poll_id'])) {
            return false;
        }
        $params = array('poll_id' => $filter['poll_id']);
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
   with tb1 as (
select a1.city_id
, sum(voters_online) + sum(voters_sms) + sum(voters_offline) voters_total
from stats_prev_ppp3 a1
where a1.poll_id = :poll_id
group by a1.city_id
), tb2 as (
select a1.id city_id, a1.name city_name, a1.ibge_code, a1.corede_id, a2.name corede_name, a1.tot_voter tot_pop
, (
select perc_apply from rl_criterio_mun 
where  1 = 1
and coalesce(limit_citizen, a1.tot_voter) >= a1.tot_voter
and type_calc = 1 
and poll_id = :poll_id
order by limit_citizen asc
limit 1
) perc_pop
, (
select perc_apply from rl_criterio_mun 
where  1 = 1
and coalesce(limit_citizen, a1.tot_voter) >= a1.tot_voter
and type_calc = 2 
and poll_id = :poll_id
order by limit_citizen asc
limit 1
) perc_prog
from city a1
inner join corede a2 on a2.id = a1.corede_id
),
-- start prog --
tb3 as (
select corede_id, option_id, sum(tot) tot_corede
from stats_prev_ppp2 
where poll_id = :poll_id
group by corede_id, option_id
), tb4 as (
select  
a1.city_id
, a1.option_id
, a1.tot tot_in_city
, a2.title option_name
, (a1.tot::numeric*100)/tb3.tot_corede perc_in_corede
from stats_prev_ppp2 a1
inner join poll_option a2 on a2.id = a1.option_id
inner join tb3 on tb3.corede_id = a1.corede_id and tb3.option_id = a1.option_id
where a1.poll_id = :poll_id
)
-- end prog --
, tb5 as (
select a1.city_id
, sum(tot_votes_online) + sum(tot_votes_sms) + sum(tot_votes_offline) votes_total
from stats_prev_ppp a1
where a1.poll_id = :poll_id
group by a1.city_id
)
select 
tb2.city_id
, tb2.city_name
, tb2.ibge_code
, tb2.corede_id
, tb2.corede_name
, tb2.tot_pop
, tb2.perc_pop
, tb2.perc_prog
, (tb2.tot_pop*tb2.perc_pop)/100 corte_mun
, (tb2.tot_pop*tb2.perc_prog)/100 corte_prog
, tb1.voters_total
, tb5.votes_total
, (tb1.voters_total::decimal*100)/tb2.tot_pop voters_perc
, case when tb1.voters_total >= ((tb2.tot_pop*tb2.perc_pop)/100) then \'CLASSIFICADO\' ELSE \'DESCLASSIFICADO\' END status_corte_mun
, coalesce(sum(case when tb4.perc_in_corede >= (tb2.perc_prog) then 1 else null end),0) tot_prog_classificados
from tb2 
left join tb1 on tb1.city_id = tb2.city_id
left join tb5 on tb5.city_id = tb2.city_id
left join tb4 on tb4.city_id = tb2.city_id
where 1 = 1 ';
        if (isset($filter['corede_id']) && $filter['corede_id']) {
            $params['corede_id'] = $filter['corede_id'];
            $sql .= "and tb2.corede_id = :corede_id ";
        }
        if (isset($filter['city_id']) && $filter['city_id']) {
            $params['city_id'] = $filter['city_id'];
            $sql .= "and tb2.city_id = :city_id ";
        }

$sql .= 'group by 
tb2.city_id
, tb2.city_name
, tb2.ibge_code
, tb2.corede_id
, tb2.corede_name
, tb2.tot_pop
, tb2.perc_pop
, tb2.perc_prog
, tb1.voters_total
, tb5.votes_total
order by tb2.city_name
        ';
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function findEspecial3($filter)
    {
        if (!isset($filter['poll_id'])) {
            return false;
        }
        $params = array('poll_id' => $filter['poll_id']);
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
   with tb1 as (
select a1.city_id
, sum(voters_online) + sum(voters_sms) + sum(voters_offline) voters_total
from stats_prev_ppp3 a1
where a1.poll_id = :poll_id
group by a1.city_id
--order by a1.city_id
), tb2 as (
select a1.id city_id, a1.name city_name, a1.ibge_code, a1.corede_id, a2.name corede_name, a1.tot_voter tot_pop
, (
select perc_apply from rl_criterio_mun 
where  1 = 1
and coalesce(limit_citizen, a1.tot_voter) >= a1.tot_voter
and type_calc = 1 
and poll_id = :poll_id
order by limit_citizen asc
limit 1
) perc_pop
, (
select perc_apply from rl_criterio_mun 
where  1 = 1
and coalesce(limit_citizen, a1.tot_voter) >= a1.tot_voter
and type_calc = 2 
and poll_id = :poll_id
order by limit_citizen asc
limit 1
) perc_prog
from city a1
inner join corede a2 on a2.id = a1.corede_id
),
-- start prog --
tb3 as (
select corede_id, option_id, sum(tot) tot_corede
, rank() OVER (partition by corede_id order by sum(tot) desc ) rank_in_corede
from stats_prev_ppp2 
where poll_id = :poll_id
group by corede_id, option_id
)
, tb4 as (
select  
a1.corede_id
,a1.city_id
, a1.option_id
, a1.tot tot_in_city
, a2.title option_name
, (a1.tot::numeric*100)/tb3.tot_corede perc_in_corede
, tb3.rank_in_corede
from stats_prev_ppp2 a1
inner join poll_option a2 on a2.id = a1.option_id
inner join tb3 on tb3.corede_id = a1.corede_id and tb3.option_id = a1.option_id
where a1.poll_id = :poll_id
order by corede_id, city_id, option_id
)
-- end prog --
, tb5 as (
select a1.city_id
, sum(tot_votes_online) + sum(tot_votes_sms) + sum(tot_votes_offline) votes_total
from stats_prev_ppp a1
where a1.poll_id = :poll_id
group by a1.city_id
)
select 
tb2.city_id
, tb2.city_name
, tb2.ibge_code
, tb2.corede_id
, tb2.corede_name
, tb2.perc_prog
, tb4.tot_in_city
, tb4.option_id
, tb4.option_name
, tb4.perc_in_corede
, tb4.rank_in_corede            
, case when tb4.perc_in_corede >= (tb2.perc_prog) then \'CLASSIFICADO\' ELSE \'DESCLASSIFICADO\' END status_prog_classificados
, case when tb1.voters_total >= ((tb2.tot_pop*tb2.perc_pop)/100) then \'CLASSIFICADO\' ELSE \'DESCLASSIFICADO\' END status_corte_mun
, case when tb1.voters_total >= ((tb2.tot_pop*tb2.perc_pop)/100) and tb4.perc_in_corede >= (tb2.perc_prog) then \'CLASSIFICADO\' ELSE \'DESCLASSIFICADO\' END status_combinado_prog_classificados
from tb2 
left join tb1 on tb1.city_id = tb2.city_id
left join tb5 on tb5.city_id = tb2.city_id
left join tb4 on tb4.city_id = tb2.city_id
where 1 = 1 
            ';
        
        if (isset($filter['corede_id']) && $filter['corede_id']) {
            $params['corede_id'] = $filter['corede_id'];
            $sql .= "and tb2.corede_id = :corede_id ";
        }
        if (isset($filter['city_id']) && $filter['city_id']) {
            $params['city_id'] = $filter['city_id'];
            $sql .= "and tb2.city_id = :city_id ";
        }
        $sql .= 'order by tb2.corede_name, tb2.city_name, tb4.option_name ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    /**
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function findEspecial4($filter)
    {
        if (!isset($filter['poll_id'])) {
            return false;
        }
        $params = array('poll_id' => $filter['poll_id']);
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
with tb1 as (
select 
a1.poll_id
, a1.corede_id
, a4.name corede_name
, a1.option_id
, a2.title option_name
, a2.rl_agency_id
, a3.name rl_agency_name
, sum(a1.tot) tot_corede
, rank() OVER (partition by a1.corede_id order by sum(a1.tot) desc ) rank_in_corede
from corede a4
left join stats_prev_ppp2 a1 on a1.corede_id = a4.id and a1.poll_id = :poll_id and a1.option_id is not null
left join poll_option a2 on a2.id = a1.option_id
left join rl_agency a3 on a3.id = a2.rl_agency_id
group by a1.poll_id, a1.corede_id, a4.name, a1.option_id, a2.title, a2.rl_agency_id, a3.name
)
select 
a1.corede_id
, a1.corede_name
, a1.option_id
, a1.option_name
, a1.rl_agency_id
, a1.rl_agency_name
, a1.tot_corede
, a1.rank_in_corede
, a5.tot_program
, case when a1.rank_in_corede <= a5.tot_program then \'SIM\' else \'NÃO\' end classificado
, 
case when a1.rank_in_corede <= a5.tot_program then
case a1.rank_in_corede 
when 1 then (a5.tot_value * a5.program1)/100 
when 2 then (a5.tot_value * a5.program2)/100 
when 3 then (a5.tot_value * a5.program3)/100 
when 4 then (a5.tot_value * a5.program4)/100
when 5 then (a5.tot_value * a5.program5)/100
end 
end tot_value_calc
, 
case when a1.rank_in_corede <= a5.tot_program then
case a1.rank_in_corede 
when 1 then a5.program1 
when 2 then a5.program2 
when 3 then a5.program3 
when 4 then a5.program4
when 5 then a5.program5
end 
end perc_value_calc            
from tb1 a1
left join rl_criterio a5 on a5.poll_id = a1.poll_id and a5.corede_id = a1.corede_id
where 1 = 1
        ';
        
        if (isset($filter['corede_id']) && $filter['corede_id']) {
            $sql .= 'and a1.corede_id = :corede_id ';
            $params['corede_id'] = $filter['corede_id'];
        }
        if (isset($filter['classificado']) && $filter['classificado']) {
            if ($filter['classificado'] == 1) {
                $sql .= 'and a1.rank_in_corede <= a5.tot_program ';
            } else {
                $sql .= 'and a1.rank_in_corede > a5.tot_program ';
            }
        }
        $sql .= 'order by a1.corede_name, a1.rank_in_corede ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function findEspecial5($filter)
    {
        if (!isset($filter['poll_id'])) {
            return false;
        }
        $params = array('poll_id' => $filter['poll_id']);
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = '
with tb1 as (
select
a1.poll_id
, a1.corede_id
, a4.name corede_name
, a1.option_id
, a2.title option_name
, a2.rl_agency_id
, a3.name rl_agency_name
, sum(a1.tot) tot_corede
, rank() OVER (partition by a1.corede_id order by sum(a1.tot) desc ) rank_in_corede
from corede a4
left join stats_prev_ppp2 a1 on a1.corede_id = a4.id and a1.poll_id = :poll_id and a1.option_id is not null
left join poll_option a2 on a2.id = a1.option_id
left join rl_agency a3 on a3.id = a2.rl_agency_id
group by a1.poll_id, a1.corede_id, a4.name, a1.option_id, a2.title, a2.rl_agency_id, a3.name
), tb2 as (
select
a1.*
, case when a1.rank_in_corede <= a5.tot_program then \'SIM\' else \'NÃO\' end classificado
,
case when a1.rank_in_corede <= a5.tot_program then
case a1.rank_in_corede
when 1 then (a5.tot_value * a5.program1)/100
when 2 then (a5.tot_value * a5.program2)/100
when 3 then (a5.tot_value * a5.program3)/100
when 4 then (a5.tot_value * a5.program4)/100
when 5 then (a5.tot_value * a5.program5)/100
end
end tot_value_calc
from tb1 a1
left join rl_criterio a5 on a5.poll_id = a1.poll_id and a5.corede_id = a1.corede_id
)
select 
rl_agency_id
, rl_agency_name
, option_name
, coalesce(sum(case when classificado = \'SIM\' then tot_value_calc else null end),0) tot_value_calc
from tb2
group by 
rl_agency_id
, rl_agency_name
, option_name

order by option_name
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
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
            
            $sql = 'insert into rl_criterio (corede_id, poll_id, tot_value, tot_program, program1, program2, program3, program4, program5) values (?,?,?,?,?,?,?,?,?)';
            $stmt = $conn->prepare($sql);
            foreach ($items as $key => $val) {                
                $stmt->execute(array($key, n($pollId), n($val['tot_value']), n($val['tot_program']), n($val['program1']), n($val['program2']), n($val['program3']), n($val['program4']), n($val['program5'])));
            }
            $conn->commit();            
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
        return true;
    }
}
