<?php

class Local extends AppController {

	public static function index() {
		self::setTitle("Locais de votação");

		if (self::getParam('city_id'))
			$city_id = self::getParam('city_id');
		if (self::getParam('string'))
			$string = self::getParam('string');

		$html = new HTMLHelper();

		self::addJavascript('/js/jquery-1.7.2.js');
		self::addJavascript('/js/locais.js');
//		TODO: desnecessario
//		self::setJavascriptVar('back', $html->url(array('controller' => 'Local', 'action' => 'index')));
		self::setJavascriptVar('municipio', $html->url(array('controller' => 'Local', 'action' => 'municipio')));

		
		$urna_municipios = Urna::findByTxtLocalizacao();
		
//		$coredes = Regiao::findAll();		
//		$Object = Municipio::objectToSQLArray($municipios->get);
		
		
		$city_select = self::carregarSelectMunicipio($urna_municipios);
		$string = self::descricaoMunicipios($urna_municipios);

//		$corede_settings = array('id_name' => 'id_corede', 'label_name' => 'nm_corede');
//		$corede_select = $html->select($corede, 2, $corede_settings);

		self::render(compact("city_select", "string"));
	}

	public static function municipio() {
		$city_id = self::getParam('city_id');
		$urna_municipios = Urna::findByTxtLocalizacao();
		$string = self::descricaoMunicipios($urna_municipios, $city_id);
		self::setJavascriptVar('city_id', $city_id);

		self::render(compact("string"));
	}

	public static function descricaoMunicipios($municipios, $city_id = -1) {
		if ($city_id == -1) {
			$string = "";
			$string .= <<<EOT
					<ul>
EOT;
			foreach ($municipios as $key) {
				if (is_string($key->getNmMunicipio())) {
					$name = $key->getNmMunicipio();
					$string .= <<<EOT
					<li> --> "$name"...</li>
EOT;
				}
			}
			$string .= <<<EOT
					</ul>
EOT;
		} else {
			$string = "";
			$string .= <<<EOT
					<ul>
EOT;
			foreach ($municipios as $key) {
				if (is_string($key->getNmMunicipio()) && $key->getIdMunicipio() == $city_id) {
					$name = $key->getNmMunicipio();
					$string .= <<<EOT
					<li> --> "$name"...</li>
EOT;
				}
			}
			$string .= <<<EOT
					</ul>
EOT;
		}
		return $string;
	}

	public static function carregarSelectMunicipio($municipios) {

		$html = new HTMLHelper();
		$city_attributes = array("class" => "municipio");
		$city_settings = array('default_option' => 'Escolha a cidade...', 'id_name' => 'id_municipio', 'label_name' => 'nm_municipio');
		$default = -1;
		$city_select = $html->select($municipios, $default, $city_settings, $city_attributes);

		return $city_select;
	}

}