<?php

class Cedulas extends AppController {

	public static function index() {

		self::setTitle("Cédulas por Região");
		self::addCSS('/css/estilos_internas.css');

		$html = new HTMLHelper();

		self::setJavascriptVar('regiao', $html->url(array('controller' => 'Cedulas', 'action' => 'consulte')));
		self::addJavascript('/js/jquery-1.7.2.min.js');
		self::addJavascript('/js/regiao_cedulas.js');

		$options_regiao = Regiao::findAll();
		$regiao_attr = array("class" => "regiao");
		$regiao_settings = array('default_option' => 'escolha a região...', 'id_name' => 'id_regiao', 'label_name' => 'nm_regiao');
		$default = -1;

		$select_regiao = $html->select($options_regiao, $default, $regiao_settings, $regiao_attr);

		self::render(compact("select_regiao"));
	}

	public static function consulte() {

		self::setTitle("Cédula da Região");
		self::addCSS('/css/estilos_internas.css');

		$regiao_id = -1;
		if (self::getParam('regiao_id'))
			$regiao_id = self::getParam('regiao_id');

		if (isset($regiao_id) && $regiao_id != -1) {
			$votacao = reset(Votacao::findByActiveVotacao());
			$grupos = $votacao->findGruposDemanda();
			$areas = $votacao->findAreasTematicas($regiao_id);
			$options = array();
			foreach ($grupos as $grupo) {
				$options[$grupo->getIdGrupoDemanda()] = $grupo->getOptions($regiao_id);
			}
		}

		self::render(compact('grupos', 'areas', 'options'));
	}


}