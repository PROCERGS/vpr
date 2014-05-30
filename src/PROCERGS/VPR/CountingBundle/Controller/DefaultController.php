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
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($pollId);

        $voteRepo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');
        $votes = $voteRepo->findBy(array('corede' => 19));
        //$votes = $voteRepo->findByPoll($poll);

        $privateKeyFile = $this->container->getParameter('privateKeyFile');
        $privatePollKey = openssl_pkey_get_private($privateKeyFile, 'test');
        $serializer = $this->container->get('jms_serializer');
        $openVotes = array();
        $votedOptions = array();
        $optionsCount = array();
        foreach ($votes as $vote) {
            $openVote = $vote->openVote($privatePollKey);
            $openVotes[] = $openVote;
            $openOptions = $serializer->deserialize($openVote->getPlainOptions(),
                    'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\PollOption>',
                    'json');
            $openVote->setPollOption($openOptions);

            foreach ($openOptions as $option) {
                $id = 'option' . $option->getId();
                if (array_key_exists($id, $optionsCount)) {
                    $optionsCount[$id] += 1;
                } else {
                    $optionsCount[$id] = 1;
                }
                $votedOptions[$id] = $option;
                $logger->info("1 vote for $id");
            }
        }

        return compact('openVotes', 'votedOptions', 'optionsCount');
    }

}
