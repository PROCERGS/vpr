<?php

class Cedulas extends AppController {

	public static function before() {
		parent::before();
		self::setPageName("Consulta de Cédulas de Votação");
		self::setPagesubName("Votação de Prioridades - Orçamento 2013");
	}

	public static function index() {

		self::setTitle("Cédulas por Região");

		$html = new HTMLHelper();

		$grupo_urnas = Urna::findByTxtLocalizacaoAgrup();
		$array = array();
		foreach ($grupo_urnas as $urna) {
			array_push($array, $urna->getNmMunicipio());
		}
		$string_array = implode("|", $array);

		self::addCSS('/css/jquery-ui-1.8.21.custom.css');
		self::addCSS('/css/cedulas.css');
		self::setJavascriptVar('grupo_urnas', $string_array);
		self::setJavascriptVar('regiao_municipio', $html->url(array('controller' => 'Cedulas', 'action' => 'regiao_municipio')));
		self::addJavascript('/js/jquery-1.7.2.min.js');
		self::addJavascript('/js/jquery-ui-1.8.21.custom.min.js');
		self::addJavascript('/js/jquery.ui.autocomplete.js');
		self::addJavascript('/js/cedulas.js');


		self::setJavascriptVar('regiao', $html->url(array('controller' => 'Cedulas', 'action' => 'consulte')));

		$options_regiao = Regiao::findAll();
		$regiao_attr = array("class" => "regiao", 'name' => 'regiao_id', 'id' => 'regiao_id');
		$regiao_settings = array('default_option' => '--------------', 'id_name' => 'id_regiao', 'label_name' => 'nm_regiao');
		$default = -1;

		$select_regiao = $html->select($options_regiao, $default, $regiao_settings, $regiao_attr);

		self::render(compact("select_regiao"));
	}

	public static function consultar() {

		self::setTitle("Consulta Cédula de Votação de Prioridades - Orçamento 2013");
		self::addCSS('/css/cedulas.css');

		$regiao_id = -1;
		if (self::getParam('regiao_id'))
			$regiao_id = self::getParam('regiao_id');

		if (isset($regiao_id) && $regiao_id != -1) {

			$votacao = Votacao::findByActiveVotacaoRead();

			if (!empty($votacao)) {
				$votacao = Votacao::cast(reset($votacao));
				$grupos = $votacao->findGruposDemanda();
				$areas = $votacao->findAreasTematicas($regiao_id);

				$groups = array();
				foreach ($grupos as $grupo) {
					$groups[$grupo->getIdGrupoDemanda()] = array(
						'group' => $grupo,
						'areasTematicas' => $grupo->getAreasTematicas($regiao_id)
					);

					if ($grupo->getFgTituloSimples() != 1)
						$groups[$grupo->getIdGrupoDemanda()]['areas'] = $grupo->getOptionsGroupByAreaTematica($regiao_id);
					else
						$groups[$grupo->getIdGrupoDemanda()]['options'] = $grupo->getOptions($regiao_id);
				}
			}

			AppController::setRegiao($regiao_id);
		}

		$readonly = TRUE;

		self::render(compact('groups', 'readonly'), array('controller' => 'Election', 'action' => 'step'));
	}

	public static function regiao_municipio() {
		$nome_municipio = self::getParam('nome_municipio');
		$municipios = Municipio::findByNmMunicipio($nome_municipio);
		$municipios = reset($municipios);
		$mun = $municipios->getIdRegiao();
		self::render(compact('mun'));
	}

}