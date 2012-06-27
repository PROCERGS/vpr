<?php

class Cedulas extends AppController {

	public static function before() {
		parent::before();
		self::setPageName("Consulta de Cédulas de Votação");
	}
	
	public static function index() {

		self::setTitle("Cédulas por Região");

		$html = new HTMLHelper();

		self::setJavascriptVar('regiao', $html->url(array('controller' => 'Cedulas', 'action' => 'consulte')));

		$options_regiao = Regiao::findAll();
		$regiao_attr = array("class" => "regiao", 'name' => 'regiao_id', 'id' => 'regiao_id');
		$regiao_settings = array('default_option' => '--------------', 'id_name' => 'id_regiao', 'label_name' => 'nm_regiao');
		$default = -1;

		$select_regiao = $html->select($options_regiao, $default, $regiao_settings, $regiao_attr);

		self::render(compact("select_regiao"));
	}

	public static function consultar() {

		self::setTitle("Cédula da Região");

		$regiao_id = -1;
		if (self::getParam('regiao_id'))
			$regiao_id = self::getParam('regiao_id');

		if (isset($regiao_id) && $regiao_id != -1) {
			
			$votacao = Votacao::findByActiveVotacao();
			
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
			
			self::setJavascriptVar('current_id_regiao', $regiao_id);
		}
		
		$readonly = TRUE;
		
		self::render(compact('groups', 'readonly'), array('controller' => 'Election', 'action' => 'step'));
	}

}