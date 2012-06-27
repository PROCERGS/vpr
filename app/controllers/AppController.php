<?php
class AppController extends Controller {
	
	private static $page_name = '';
	private static $page_subname = '';
	private static $regiao = NULL;

	protected static function setDefaultStylesheets() {
		self::addCSS('/css/1140.css');
		self::addCSS('/css/styles.css');
	}
	protected static function setDefaultJavascripts() {
		self::addJavascript('/js/jquery-1.7.2.min.js');
		self::addJavascript('/js/css3-mediaqueries.js');
		self::addJavascript('/js/default.js');
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
		
		$regiao = self::getRegiao();
		$current_nm_regiao = '';
		if (!is_null($regiao)) {
			self::setJavascriptVar('current_id_regiao', $regiao->getIdRegiao());
			$current_nm_regiao = $regiao->getNmRegiao();
		} elseif (!is_null($currentUser)) {
			self::setJavascriptVar('current_id_regiao', $currentUser->getRegiao()->getIdRegiao());
			$current_nm_regiao = $currentUser->getRegiao()->getNmRegiao();
		}
		
		$html = new HTMLHelper();
		$detector = Session::get('detector');
		$values = array_merge($values, compact('html', 'detector', 'current_nm_regiao'));
		
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
	
		if (Cidadao::isAuthenticated() && $after_login !== FALSE) {
			if (strlen($after_login) > 0) {
				self::clearDestinationAfterLogin();
				self::redirect($after_login);
			}
		}
	}
	
	protected static function setPageName($page_name) { self::$page_name = $page_name; }
	protected static function getPageName() { return self::$page_name; }
	
	protected static function setPageSubName($page_subname) { self::$page_subname = $page_subname; }
	protected static function getPageSubName() { return self::$page_subname; }
	
	/**
	 * @return Regiao
	 */
	public static function getRegiao() { return self::$regiao; }
	public static function setRegiao($regiao) {
		if ($regiao instanceof Regiao)
			self::$regiao = $regiao;
		elseif (is_numeric($regiao))
			self::$regiao = reset(Regiao::findByIdRegiao($regiao));
		else
			throw new InvalidArgumentException('Formato de região não reconhecido.');
	}
}
