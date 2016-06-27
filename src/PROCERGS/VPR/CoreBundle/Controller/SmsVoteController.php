<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use JMS\Serializer\Serializer;
use PROCERGS\VPR\CoreBundle\Exception\SmsServiceException;
use PROCERGS\VPR\CoreBundle\Security\SmsVoteHandler;
use PROCERGS\VPR\CoreBundle\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

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

        try {
            $votes = $smsVoteHandler->processPendingSms($em, $smsService);

            return new JsonResponse(
                [
                    'votes' => count($votes),
                ]
            );
        } catch (ServiceUnavailableHttpException $e) {
            return new JsonResponse(
                [
                    'votes' => 0,
                    'error' => $e->getMessage(),
                ], Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}
