<?php

namespace PROCERGS\VPR\CountingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Poll;

class DefaultController extends Controller
{
    /**
     * @Route("/count/{pollId}")
     * @Template()
     */
    public function indexAction($pollId)
    {
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($pollId);

        $voteRepo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');
        $votes = $voteRepo->findByPoll($poll);
        var_dump($votes); die();

        return array('name' => $name);
    }
}
