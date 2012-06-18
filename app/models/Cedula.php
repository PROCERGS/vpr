<?php
class Cedula extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_cedula;
	protected $id_votacao;
	protected $id_regiao;
	protected $id_grupo_demanda;
	protected $id_area_tematica;
	protected $nm_demanda;
	protected $ds_demanda;
	protected $vlr_demanda;
	protected $cod_projeto_sms;
	protected $nm_projeto_cedula;
	protected $int_ordem;

	/**
	 * @return Cedula
	 */
	public static function cast($o) { return $o; }
	
	public static function findByGrupoDemandaVotacaoRegiao($id_grupo_demanda, $id_votacao, $id_regiao) {
		$query = PDOUtils::getConn()->prepare(CedulaQueries::SQL_FIND_BY_GRUPO_DEMANDA_AND_VOTACAO_AND_REGIAO);
		$query->bindValue('id_grupo_demanda', $id_grupo_demanda);
		$query->bindValue('id_votacao', $id_votacao);
		$query->bindValue('id_regiao', $id_regiao);
		
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
}