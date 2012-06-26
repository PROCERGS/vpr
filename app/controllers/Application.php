<?php

class Application extends AppController {

	public static function index() {

		$string_alt_img = null;

		self::addCSS('/css/estilos_capa.css');

		$html = new HTMLHelper();

		$votacao = Votacao::findAll();
		
		$show_vote_now = FALSE;
		foreach ($votacao as $vot) {
			$data_ini = $vot->getDthInicio();
			$data_fim = $vot->getDthFim();
			
			$data_ini = Util::createDateTime($vot->getDthInicio(), 'Y-m-d H:i:s');
			$data_fim = Util::createDateTime($vot->getDthFim(), 'Y-m-d H:i:s');
			$string_data = self::stringDate($data_ini, $data_fim);

			$show_vote_now = $vot->isOpen();

			$nome = $vot->getNmVotacao();
			$orçamento = (int) $vot->getIntExercicio() + 1;
			if (is_string($nome) && is_string($orçamento))
				$string_alt_img = $nome + " - " + $orçamento;
		}


		self::render(compact("string_data", "string_alt_img", "show_vote_now"));
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

	private static function stringDate($data_ini, $data_fim) {
		$now = new DateTime();
		$current_mk = time();

		if ($now <= $data_fim) {
			try {
				/**
				 * 	Se dias diferentes 
				 */
				if ($data_ini < $data_fim && $data_ini->diff($data_fim)->days > 0) {
					/**
					 * 	Se dias diferentes e votação ocorrendo 
					 */
					if ($data_ini <= $now && $data_fim >= $now) {
						$string_data = strftime("A votação está ocorrendo, você pode votar até às %H horas do dia %d de %B", $data_fim->getTimestamp());
					}
					/**
					 * 	Se dias diferentes e votação ainda não está ocrrendo 
					 */ else {
						$string_data = strftime("Dia %d de %B  às %H horas", $data_ini->getTimestamp());
						$string_data .= " até ";
						$string_data .= strftime("dia %d de %B às %H horas de %Y", $data_fim->getTimestamp());
					}
				}
				/**
				 * 	Se dias iguais 
				 */ else if ((strftime("%d", $data_ini->getTimestamp()) == strftime("%d", $data_fim->getTimestamp()))) {
					/**
					 * 	Se dias iguais e votação ocorrendo 
					 */
					if ($data_ini <= $now && $data_fim >= $now) {
						$string_data = strftime("A votação está ocorrendo, você pode votar até às %H horas", $data_fim->getTimestamp());
					}
					/**
					 * 	Se dias iguais e votação ainda não está ocrrendo  
					 */
					else
						$string_data = strftime("Dia %d de %B de %Y a partir das %H horas e ocorrerá até às ", $data_ini->getTimestamp()) . strftime("%H horas", $data_fim->getTimestamp());
				}
			} catch (Exception $e) {
				$e->getMessage();
			}
		} else {
			$string_data = "A votação, já ocorreu em " . strftime("%d de %B de %Y", $data_ini->getTimestamp());
		}

		/**
		 * 	Caso queira utilizar um formato padrão de data 
		 * 	descomente as linhas de baixo
		 */
//		$string_data = strftime("Dia %d de %B  às %H horas", $data_ini);
//		$string_data .= " até ";
//		$string_data .= strftime("às %H horas de %Y", $data_fim);

		return $string_data;
	}

}
