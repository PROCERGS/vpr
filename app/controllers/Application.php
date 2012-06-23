<?php

class Application extends AppController {

	public static function index() {

		$string_alt_img = null;
		$button = null;
		setlocale(LC_TIME, "portuguese");
		$data_completa = strftime("São %H horas e %m minutos");
		//echo $data_completa;

		self::addCSS('/css/estilos_capa.css');

		$html = new HTMLHelper();

		$votacao = Votacao::findAll();

		foreach ($votacao as $vot) {
			$data_ini = $vot->getDthInicio();
			$data_fim = $vot->getDthFim();

			$data_ini = self::mk_time($data_ini);
			$data_fim = self::mk_time($data_fim);
			$string_data = self::stringDate($data_ini, $data_fim);

			$link_start_vot = $html->link("Para votar, clique aqui!", array('controller' => 'Election', 'action' => 'start'));

			if (self::votacaoOcorrendo($data_ini, $data_fim))
				$button = <<<EOD
					<div class="row vote_now">
					<div class="twelvecol last">
						$link_start_vot
					</div>
				</div>
EOD;

			$nome = $vot->getNmVotacao();
			$orçamento = (int) $vot->getIntExercicio() + 1;
			if (is_string($nome) && is_string($orçamento))
				$string_alt_img = $nome + " - " + $orçamento;
		}


		self::render(compact("string_data", "string_alt_img", "button"));
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

	public static function mk_time($data) {
		$month = substr($data, 5, 2);
		$date = substr($data, 8, 2);
		$year = substr($data, 0, 4);
		$hour = substr($data, 11, 2);
		$minutes = substr($data, 14, 2);
		$seconds = substr($data, 17, 4);

		$data = mktime($hour, $minutes, $seconds, $month, $date, $year);

		return $data;
	}

	public static function stringDate($data_ini, $data_fim) {
		$current_mk = time();

		if ($current_mk <= $data_fim) {
			try {
				/**
				 * 	Se dias diferentes 
				 */
				if ($data_ini < $data_fim && (strftime("%d", $data_ini) < strftime("%d", $data_fim))) {
					/**
					 * 	Se dias diferentes e votação ocorrendo 
					 */
					if (self::votacaoOcorrendo($data_ini, $data_fim)) {
						$string_data = strftime("A votação está ocorrendo, você pode votar até às %H horas do dia %d de %B", $data_fim);
					}
					/**
					 * 	Se dias diferentes e votação ainda não está ocrrendo 
					 */ else {
						$string_data = strftime("Dia %d de %B  às %H horas", $data_ini);
						$string_data .= " até ";
						$string_data .= strftime("dia %d de %B às %H horas de %Y", $data_fim);
					}
				}
				/**
				 * 	Se dias iguais 
				 */ else if ((strftime("%d", $data_ini) == strftime("%d", $data_fim))) {
					/**
					 * 	Se dias iguais e votação ocorrendo 
					 */
					if (self::votacaoOcorrendo($data_ini, $data_fim)) {
						$string_data = strftime("A votação está ocorrendo, você pode votar até às %H horas", $data_fim);
					}
					/**
					 * 	Se dias iguais e votação ainda não está ocrrendo  
					 */
					else
						$string_data = strftime("Dia %d de %B de %Y a partir das %H horas e ocorrerá até às ", $data_ini) . strftime("%H horas", $data_fim);
				}
			} catch (Exception $e) {
				$e->getMessage();
			}
		} else {
			$string_data = "A votação, já ocorreu em " . strftime("%d de %B de %Y", $data_ini);
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

	public static function votacaoOcorrendo($data_ini, $data_fim) {
		$current_mk = time();
		if ($current_mk <= $data_fim && $current_mk >= $data_ini)
			return true;
		else
			return false;
	}

}
