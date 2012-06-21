<?php
class Municipio extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_municipio;
	protected $id_uf;
	protected $id_regiao;
	protected $nm_municipio;
	protected $cod_mun_tre;
	protected $cod_mun_ibge;
	protected $cod_mun_sefa;
	protected $qtd_populacao;
	protected $qtd_eleitores;
	protected $cod_mun_pop;
	protected $fg_capital;
	protected $int_ordem;

	/**
	 * @return Municipio
	 */
	public static function cast($o) { return $o; }
	
	public static function findMunicipioUfByNmMunicipio($value) {
		$value = "%$value%";
		$urna = Urna::getTable();
		$cidade = self::getTable();
		$sql = "SELECT * FROM $urna u INNER JOIN $cidade m ON m.`id_municipio` = u.`id_municipio` WHERE m.`nm_municipio` LIKE :nm_municipio ORDER BY m.`nm_municipio` ASC, u.`id_urna`";
		$query = PDOUtils::getConn()->prepare($sql);
		$query->bindValue('nm_municipio', $value);
		$query->execute();
		$data = $query->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
}
