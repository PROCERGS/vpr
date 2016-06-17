<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianPhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Security\SmsVoteHandler;
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
        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->get('sms.vote_handler');

        $from = BrazilianPhoneNumberFactory::createFromE164($request->get('de'));

        $sms = new Sms();
        $sms
            ->setMessage($request->get('message'))
            ->setFrom($from);

        return array();
    }
}
