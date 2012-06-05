<?php
class AppController extends Controller {

	protected static function setDefaultStylesheets() {
		self::addCSS('/css/1140.css');
		self::addCSS('/css/styles.css');
	}
	protected static function setDefaultJavascripts() {
		self::addJavascript('/js/css3-mediaqueries.js');
	}
	
	public static function before() {
		parent::before();
		
		self::checkAfterLoginRedirect();
		
		$class = get_called_class();
		$class::setDefaultStylesheets();
		$class::setDefaultJavascripts();
		
		$detector = new Mobile_Detect();
		$isMobile = $detector->isMobile();
		$subDomainMobile = Util::startsWith($_SERVER['HTTP_HOST'], 'm.');
		
		Session::set('detector', $detector);
		if ($isMobile || $subDomainMobile) {
			Config::set('isMobile', TRUE);
			Config::set('votes.pageSize', 5);
		}
	}
	
	public static function beforeRender($values = array()) {
		$values = parent::beforeRender($values);
		
		$votingSession = VotingSession::getCurrentVotingSession();
		if (!is_null($votingSession))
			$currentUser = $votingSession->getCurrentUser();
		else
			$currentUser = NULL;
		if (!is_null($currentUser))
			$values = array_merge($values, compact('currentUser'));
		
		$html = new HTMLHelper();
		$detector = Session::get('detector');
		$values = array_merge($values, compact('html', 'detector'));
		
		return $values;
	}
	
	/**
	 *
	 * Defines a destination where the user shall be redirected to after loggin in.
	 * @param mixed $target
	 * @param array $target_get_params
	 */
	public static function setDestinationAfterLogin($target, $target_get_params = array()) {
		Session::set('after_login', $target);
		Session::set('after_login_get_params', $target_get_params);
	}
	
	/**
	 *
	 * Returns the URL of the destination after login or FALSE if there is none.
	 */
	public static function getDestinationAfterLogin() {
		$after_login = Session::get('after_login');
		$after_login_get_params = Session::get('after_login_get_params');
	
		if (!is_null($after_login)) {
			if (is_array($after_login))
				$after_login = $html->url($after_login, $after_login_get_params);
				
			return $after_login;
		}
		return FALSE;
	}
	
	public static function clearDestinationAfterLogin() {
		Session::delete('after_login');
		Session::delete('after_login_get_params');
	}
	
	/**
	 *
	 * Verifies if the user need to be redirected and redirects him.
	 */
	protected static function checkAfterLoginRedirect() {
		$html = new HTMLHelper();
		$after_login = self::getDestinationAfterLogin();
	
		if (Voter::isAuthenticated() && $after_login !== FALSE) {
			if (strlen($after_login) > 0) {
				self::clearDestinationAfterLogin();
				self::redirect($after_login);
			}
		}
	}
}
