<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianPhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;
use PROCERGS\VPR\CoreBundle\Security\SmsVoteHandler;
use PROCERGS\VPR\CoreBundle\Security\VotingSessionProvider;
use PROCERGS\VPR\CoreBundle\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SmsVoteController extends Controller
{
    /**
     * @Route("/receive")
     * @Template()
     */
    public function receiveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->get('sms.vote_handler');

        /** @var SmsService $smsService */
        $smsService = $this->get('sms.service');

        $votes = $smsVoteHandler->processPendingSms($em, $smsService);

        return compact('votes');
    }
}
