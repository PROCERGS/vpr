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
	
	public static function findByTxtLocalizacao($nm_municipio,$id_votacao) {
		$query = PDOUtils::getConn()->prepare(UrnaQueries::SQL_FIND_BY_NM_MUNICIPIO);
		$query->bindValue('nm_municipio', $nm_municipio);
		$query->bindValue('id_votacao', $id_votacao);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}

}
