<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\Vote;

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

    /**
     * @param integer $id
     * @return \PROCERGS\VPR\CoreBundle\Entity\BallotBox
     */
    private function getBallotBox($id)
    {
        $polls = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        return $polls->find($id);
    }

    /**
     * @Route("/vote/add/{ballotBoxId}")
     * @Template()
     */
    public function newVoteAction($ballotBoxId)
    {
        $serializer = $this->container->get('jms_serializer');

        $person = $this->getUser();

        $poll = $this->getPoll(1);
        $ballotBox = $this->getBallotBox($ballotBoxId);

        $optionsRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $options = $optionsRepo->findByPoll($poll);

        $serializedOptions = $serializer->serialize($options, 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array('vote')));

        $vote = new Vote();
        $vote->setAuthType('lc')
                ->setVoterRegistration($person->getVoterRegistration())
                ->setPlainOptions($serializedOptions)
                ->setBallotBox($ballotBox);

        $signature = $ballotBox->sign($serializedOptions);
        $vote->setSignature($signature)->encryptVote();
        $serializedVote = $serializer->serialize($vote, 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array('vote')));

        $privateKeyFile = $this->container->getParameter('privateKeyFile');
        $privatePollKey = openssl_pkey_get_private($privateKeyFile);

        $openVote = $serializer->deserialize($serializedVote, 'PROCERGS\VPR\CoreBundle\Entity\Vote', 'json');
        $openVote->openVote($privatePollKey);

        $openOptions = $serializer->deserialize($openVote->getPlainOptions(), 'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\PollOption>', 'json');

        return compact('poll', 'options', 'serializedOptions', 'signature',
                'serializedVote', 'vote', 'openVote', 'openOptions');
    }

}
