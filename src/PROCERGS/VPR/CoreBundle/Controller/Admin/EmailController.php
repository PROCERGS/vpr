<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller.
 *
 * @Route("/public/email")
 */
class EmailController extends Controller
{

    /**
     * Lists all Person entities.
     *
     * @Route("/reminders/send", name="admin_send_reminders")
     * @Method("GET")
     */
    public function sendReminderAction()
    {
        $iterationsLimit = 2;
        $queryLimit = 10;
        $mailer = $this->get('mailer');
        $em = $this->getDoctrine()->getEntityManager();
        $sql = $em->getRepository('PROCERGSVPRCoreBundle:Person')
            ->getPendingReminderQuery($queryLimit);
        $count = $iterationsLimit;

        $registrationUrlBase = $this->getLoginCidadaoPrefilledRegistrationUrl();
        $people = array();
        while (--$count && $results = $sql->getResult()) {
            foreach ($results as &$person) {
                $message = $this->getReminderMessage($person,
                                                     $registrationUrlBase);
                $sendResult = $mailer->send($message);
                $person->setLoginCidadaoSentReminder($sendResult);
                $em->flush($person);
                $em->clear($person);
                if ($person->getLoginCidadaoSentReminder()) {
                    $people[] = $person;
                }
            }
        }
        if (null == $results) {
            $code = 204;
        } else {
            $code = 200;
        }
        return new Response('', $code);
    }

    private function nameCapitalizer($string)
    {
        $word_splitters = array(' ', '-', "O'", "L'", "D'", 'St.', 'Mc');
        $lowercase_exceptions = array('the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'", 'dos', 'da', 'do', 'dos', 'de');
        $uppercase_exceptions = array('III', 'IV', 'VI', 'VII', 'VIII', 'IX');

        $string = strtolower($string);
        foreach ($word_splitters as $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $word) {
                if (in_array(strtoupper($word), $uppercase_exceptions))
                    $word = strtoupper($word);
                else
                if (!in_array($word, $lowercase_exceptions))
                    $word = ucfirst($word);

                $newwords[] = $word;
            }

            if (in_array(strtolower($delimiter), $lowercase_exceptions))
                $delimiter = strtolower($delimiter);

            $string = join($delimiter, $newwords);
        }
        return $string;
    }

    private function getLoginCidadaoPrefilledRegistrationUrl()
    {
        $lcBase = $this->container->getParameter('login_cidadao_base_url');
        $lcPrefilledPath = $this->container->getParameter('login_cidadao_register_prefilled_path');
        return $lcBase . $lcPrefilledPath;
    }

    private function getReminderMessage(Person &$person, &$registrationUrlBase)
    {
        $params = http_build_query(array(
            'full_name' => $this->nameCapitalizer($person->getFirstName()),
            'email' => $person->getEmail(),
            'mobile' => $person->getMobile()
        ));
        $registrationUrl = "$registrationUrlBase?$params";

        $htmlBody = $this->renderView('PROCERGSVPRCoreBundle:Default:promoNfgEmail.html.twig',
                                      array(
            'fullName' => $person->getFirstName(),
            'urlCustom' => $registrationUrl
        ));

        $message = \Swift_Message::newInstance();
        $message->setSubject('Aviso');
        $message->setFrom($this->container->getParameter('mailer_sender_mail'),
                                                         $this->container->getParameter('mailer_sender_name'));
        $message->setBody($htmlBody, 'text/html')
            ->addPart($htmlBody, 'text/plain');
        $message->setTo($person->getEmail());
        return $message;
    }

}
