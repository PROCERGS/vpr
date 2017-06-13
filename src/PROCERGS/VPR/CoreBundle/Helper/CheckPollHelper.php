<?php
namespace PROCERGS\VPR\CoreBundle\Helper;
use Doctrine\ORM\EntityManager;

class CheckPollHelper
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private static $_stat1;
    public function checkBlocked($id, $type = null)
    {

        $connection = $this->em->getConnection();

        switch ($type) {
            case 'category':
                $statement = $connection->prepare('
                    WITH ss as
  (SELECT s.poll_id
   FROM category c
   INNER JOIN poll_option po ON po.category_id = c.id
   INNER JOIN step s ON s.id = po.step_id
   WHERE c.id = :category
   GROUP BY s.poll_id)
SELECT exists
  (SELECT id from ballot_box b
   INNER JOIN ss ON ss.poll_id = b.poll_id
   WHERE b.setup_at IS NOT NULL) AS downloaded,
       exists
  (SELECT v.id
   FROM vote v
   INNER JOIN ballot_box b ON b.id = v.ballot_box_id
   INNER JOIN ss ON ss.poll_id = b.poll_id) AS voted
                ');
                $statement->bindParam('category', $id);
                break;
              case 'ballotbox':
                $statement = $connection->prepare('
                    select
                        exists(select 1 from ballot_box b where b.id = :ballotbox and b.setup_at is not null limit 1) as downloaded,
                        exists(select 1 from vote v where v.ballot_box_id = :ballotbox limit 1) as voted
                ');
                $statement->bindParam('ballotbox', $id);
                break;
            default:
                if (self::$_stat1 === null) {
                    self::$_stat1 = $connection->prepare('
                         with a1 as (
 select id from ballot_box where poll_id = :poll
 )
                    select case when (select 1 from ballot_box b where b.poll_id = :poll and b.setup_at is not null limit 1) = 1 or (select 1 from vote v inner join a1 on a1.id = v.ballot_box_id limit 1) = 1 then 1 else 0 end bloqueado
                    ');
                }
                self::$_stat1->execute(array('poll'=> $id));
                
                $result = self::$_stat1->fetch(\PDO::FETCH_ASSOC);
                
                if ($result["bloqueado"]) {
                    return true;
                } else {
                    return false;
                }
                break;
        }

        $statement->execute();

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result["downloaded"] || $result["voted"]) {
            return true;
        } else {
            return false;
        }

    }


}
