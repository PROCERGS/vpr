<?php
class Regiao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_regiao;
	protected $id_uf;
	protected $id_tipo_regiao;
	protected $dt_criacao;
	protected $dt_atualizacao;
	protected $nm_regiao;
	protected $int_ordem;

	/**
	 * @return Regiao
	 */
	public static function cast($o) { return $o; }
	
	public static function findByCodMunTre($cod_mun_tre) {
		$query = PDOUtils::getConn()->prepare(RegiaoQueries::SQL_FIND_BY_COD_MUN_TRE);
		$query->bindValue('cod_mun_tre', $cod_mun_tre);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
}
