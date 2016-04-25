<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/")
 */
class SoeController extends Controller {
	
	/**
	 * @Route("/login_soe", name="login_soe")
	 * @Template()
	 */
	public function loginAction(Request $request) {
		$session = $request->getSession ();
		
		if ($request->attributes->has ( SecurityContextInterface::AUTHENTICATION_ERROR )) {
			$error = $request->attributes->get ( SecurityContextInterface::AUTHENTICATION_ERROR );
		} elseif (null !== $session && $session->has ( SecurityContextInterface::AUTHENTICATION_ERROR )) {
			$error = $session->get ( SecurityContextInterface::AUTHENTICATION_ERROR );
			$session->remove ( SecurityContextInterface::AUTHENTICATION_ERROR );
		} else {
			$error = null;
		}
		
		// last username entered by the user
		$lastUsername = (null === $session) ? '' : $session->get ( SecurityContextInterface::LAST_USERNAME );
		
		return array (
			'last_username' => $lastUsername,
			'error' => $error 
		);
	}
	
	/**
	 * @Route("/login_check", name="login_soe_check")
	 * @Template()
	 */
	public function loginCheckAction(Request $request) {
		$session = $request->getSession();
		var_dump($_REQUEST);
		die();
	}
}