<?php
class AppController extends Controller {

	protected static function setDefaultStylesheets() {
		//self::addCSS('/css/SOME.STYLESHEET.HERE.css');
	}
	protected static function setDefaultJavascripts() {
		//self::addJavascript('/js/YOUR.DEFAULT.JS.FILE.js');
	}
	
	public static function before() {
		self::setDefaultStylesheets();
		self::setDefaultJavascripts();
	}
	
	public static function beforeRender($values = array()) {
		$values = parent::beforeRender($values);
		
		$values = parent::beforeRender($values);
		$html = new HTMLHelper();
		$values = array_merge($values, compact('html'));
		
		return $values;
	}
	
}
