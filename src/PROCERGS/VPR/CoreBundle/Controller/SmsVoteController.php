<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\Sms\PhoneNumber;
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

        $from = new PhoneNumber();
        $from->setCountryCode($request->get('from_country', 55))
            ->setAreaCode($request->get('from_area'))
            ->setSubscriberNumber($request->get('from_subscriber'));

        $sms = new Sms();
        $sms
            ->setMessage($request->get('message'))
            ->setFrom($from);

        return array();
    }

}
