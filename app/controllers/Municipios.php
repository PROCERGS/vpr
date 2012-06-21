<?php
class Municipios extends AppController {
	public static function autocomplete() {
		$term = self::getParam('term');
		$municipios = Municipio::findMunicipioUfByNmMunicipio($term);
		$result = array();	
		foreach ($municipios as $data) {
			$municipio = Municipio::getFromArray($data);
			$city = $municipio->getNmMunicipio();
			
			$result[] = "$city";
		}
		echo $result;
	}
}
