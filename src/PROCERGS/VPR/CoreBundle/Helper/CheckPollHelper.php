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

    public function checkBlocked($poll)
    {
        $connection = $this->em->getConnection();

        $statement = $connection->prepare('
            select
                exists(select id from ballot_box b where b.poll_id = :poll and b.setup_at is not null) as downloaded,
                exists(select v.id from vote v inner join ballot_box b on b.id = v.ballot_box_id and b.poll_id = :poll) as voted
        ');

        $statement->bindParam('poll', $poll);
        $statement->execute();

        $result = $statement->fetch();

        if ($result["downloaded"] || $result["voted"]) {
            return true;
        } else {
            return false;
        }
    }


}
