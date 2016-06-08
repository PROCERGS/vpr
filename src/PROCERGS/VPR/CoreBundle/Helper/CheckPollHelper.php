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
            default:
                $statement = $connection->prepare('
                    select
                        exists(select id from ballot_box b where b.poll_id = :poll and b.setup_at is not null) as downloaded,
                        exists(select v.id from vote v inner join ballot_box b on b.id = v.ballot_box_id and b.poll_id = :poll) as voted
                ');
                $statement->bindParam('poll', $id);
                break;
        }

        $statement->execute();

        $result = $statement->fetch();

        if ($result["downloaded"] || $result["voted"]) {
            return true;
        } else {
            return false;
        }

    }


}
