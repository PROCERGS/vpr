<?php
class Application extends AppController {
	
	public static function index() {
		self::addCSS('/css/estilos_capa.css');
		self::render();
	}
	
	public static function make_class() {
		$className = self::getParam('class');
		$tableName = self::getParam('table');
		
		if (is_null($className))
			$className = Util::parseControllerName($tableName);
		
		$class = ClassMaker::createClass($className, $tableName);
		$class_name = ClassMaker::getClassName();
		$class_filename = ClassMaker::getClassFileName();
		
		if (self::getParam('save') == 'true') {
			$fullpath = MODELS_PATH . $class_filename;
			file_put_contents($fullpath, $class);
				
			header("Content-type: text/plain");
			echo file_get_contents($fullpath);
			return;
		}
		
		$tables = ClassMaker::getTables();
		
		self::render(compact('class', 'class_name', 'class_filename', 'tableName', 'tables'));
	}
	
}
