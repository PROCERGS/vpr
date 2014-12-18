<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller.
 *
 * @Route("/admin/email")
 */
class EmailController extends Controller
{

    /**
     * Lists all Person entities.
     *
     * @Route("/reminders/send", name="admin_send_reminders")
     * @Method("GET")
     * @Template()
     */
    public function sendReminderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $personRepo = $em->getRepository('PROCERGSVPRCoreBundle:Person');
        $people = $personRepo->getPendingReminder();

        $names = array();
        foreach ($people as $person) {
            $names[] = $this->nameCapitalizer($person->getFirstName());
        }

        return compact('people', 'names');
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

}
