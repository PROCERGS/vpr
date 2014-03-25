<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;

class DemoController extends Controller
{

    /**
     * @Route("/ballotbox/create")
     * @Template()
     */
    public function newBallotBoxAction()
    {
        $poll = $this->getPoll(1);

        $ballotBox = new BallotBox();
        $ballotBox->setName('Internet')
                ->setSecret(rand(100000, 9999999))
                ->setAddress('http://vpr.rs.gov.br/')
                ->setIsOnline(true)
                ->setPoll($poll);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ballotBox);
        $em->flush();

        return compact('ballotBox', 'poll');
    }

    /**
     * @Route("/ballotbox/view")
     * @Template()
     */
    public function viewBallotBoxAction()
    {

    }

    /**
     * @param integer $id
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll
     */
    private function getPoll($id)
    {
        $polls = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
        return $polls->find($id);
    }
}
