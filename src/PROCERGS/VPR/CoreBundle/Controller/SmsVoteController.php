<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianPhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
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
        $tag = $this->getParameter('tpd_sms_tag');

        /** @var VotingSessionProvider $votingSessionProvider */
        $votingSessionProvider = $this->get('vpr_voting_session_provider');

        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->get('sms.vote_handler');

        /** @var SmsService $smsService */
        $smsService = $this->get('sms.service');

        $ballotBox = $this->getSmsBallotBox($votingSessionProvider);

        $pendingMessages = $smsService->forceReceive($tag);

        foreach ($pendingMessages as $sms) {
            $smsVote = new SmsVote();
            $smsVote
                ->setSender($sms->de)
                ->setMessage($sms->mensagem);

            try {
                $parsed = $smsVoteHandler->parseMessage($smsVote);
                if ($parsed[SmsVoteHandler::MESSAGE_VOTER_REGISTRATION] != 20477) {
                    continue;
                }

                var_dump($sms);

                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.000-03:00', $sms->dataHoraRecebimento);
                $smsVote = new SmsVote();
                $smsVote
                    ->setSender('+'.$sms->de)
                    ->setMessage($sms->mensagem)
                    ->setReceivedAt($date);
                $vote = $smsVoteHandler->getVoteFromSmsVote($smsVote, $ballotBox);
                var_dump($vote);

                die();
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        var_dump($pendingMessages);
        die();

        return compact('pendingMessages');
    }

    /**
     * @param VotingSessionProvider $votingSessionProvider
     * @return BallotBox
     */
    private function getSmsBallotBox(VotingSessionProvider $votingSessionProvider)
    {
        /** @var BallotBoxRepository $ballotBoxRepo */
        $ballotBoxRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        /** @var BallotBox $ballotBox */
        $ballotBox = $ballotBoxRepo->findOneBy(
            [
                'isSms' => true,
                'poll' => $votingSessionProvider->getActivePoll(),
            ]
        );

        return $ballotBox;
    }
}
