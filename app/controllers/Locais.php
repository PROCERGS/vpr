<?php

class Locais extends AppController {

	public static function index() {
		self::setTitle("Locais de votação");
		self::addCSS('/css/estilos_capa.css');
		self::addCSS('/css/estilos_locais.css');
		self::addCSS('/css/jquery-ui-1.8.21.custom.css');

		$city_id = -1;
		if (self::getParam('city_id'))
			$city_id = self::getParam('city_id');

		$html = new HTMLHelper();

		$grupo_urnas = Urna::findByTxtLocalizacao();

		$array = array();
		foreach ($grupo_urnas as $urna) {
			array_push($array, $urna->getNmMunicipio());
		}

		$string_array = implode("|", $array);


		self::setJavascriptVar('grupo_urnas', $string_array);
		self::setJavascriptVar('municipio', $html->url(array('controller' => 'Locais', 'action' => 'municipio')));
		self::addJavascript('/js/jquery-1.7.2.min.js');
		self::addJavascript('/js/jquery-ui-1.8.21.custom.min.js');
		self::addJavascript('/js/jquery.ui.core.js');
		self::addJavascript('/js/jquery.ui.widget.js');
		self::addJavascript('/js/jquery.ui.position.js');
		self::addJavascript('/js/jquery.ui.autocomplete.js');
		self::addJavascript('/js/locais.js');

		self::render(compact("city_select", "string"));
	}

	public static function municipio() {
		$nome_municipio = self::getParam('nome_municipio');
		$urna_municipios = Urna::findByTxtLocalizacao();
		$string = self::descricaoMunicipios($urna_municipios, $nome_municipio);
		self::setJavascriptVar('nome_municipio', $nome_municipio);

		self::render(compact("string"));
	}

	public static function descricaoMunicipios($municipios, $nome_municipio) {
		$string = <<<EOT
					<table>
						<tr class="top_table">
						<td>Localização da urna</td>
						<td>Município</td>
						</tr>
EOT;

		foreach ($municipios as $urna_city) {

			if ($urna_city->getNmMunicipio() == $nome_municipio) {

				$nome_city = $urna_city->getNmMunicipio();
				$txt_local = $urna_city->getTxtLocalizacao();

				$string .= <<<EOT
				<tr>
				<td>$txt_local</td>
				<td>$nome_city</td>
				</tr>
EOT;
			}
		}

		return $string . "</table>";
	}

}
