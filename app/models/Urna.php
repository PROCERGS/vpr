<?php

class Urna extends Model {

	protected static $__schema = 'seplag_vpr';
	
	protected $id_urna;
	protected $id_votacao;
	protected $id_regiao;
	protected $id_municipio;
	protected $nro_urna;
	protected $txt_localizacao;

	public static function findByIdMunicipio($id_municipio) {
		$query = PDOUtils::getConn()->prepare(UrnaQueries::SQL_FIND_BY_ID_MUNINCIPIO);
		$query->bindValue('id_municipio', $id_municipio);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	public static function findByTxtLocalizacao() {
		$query = PDOUtils::getConn()->prepare(UrnaQueries::SQL_FIND_BY_TXT_LOCALIZACAO);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}

}
