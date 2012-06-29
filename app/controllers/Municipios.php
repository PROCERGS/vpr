<?php
class Municipios extends AppController {
	
	public static function search_id_regiao() {
		$nm_municipio = self::getParam('term');
		$nm_municipio = "%$nm_municipio%";
		
		$municipios = Municipio::findByNmMunicipio($nm_municipio);
		$result = array();
		foreach ($municipios as $municipio)
			$result[] = array('id' => $municipio->getIdRegiao(), 'label' => $municipio->getNmMunicipio());
		
		self::renderJSON($result);
	}
	
}
