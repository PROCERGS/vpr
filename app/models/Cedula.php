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
	protected $cod_projeto;
	protected $nm_projeto_cedula;
	protected $int_ordem;
	protected $ds_abrangencia;

	/**
	 * @return Cedula
	 */
	public static function cast($o) { return $o; }
	
	public function getLabel($nm_area_tematica = NULL) {
		if (!is_null($nm_area_tematica))
			$nm_area_tematica = trim($nm_area_tematica);
		$valor = trim($this->getVlrDemanda());
		
		$label = array();
		if (!is_null($nm_area_tematica))
			$label[] = $nm_area_tematica;
		$label[] = trim($this->getNmDemanda());
			
		if (strlen($valor) > 0 && floatval($valor) > 0)
			$label[] = 'Valor: R$'.number_format($valor, 2, ',', '.');
			
		return join(' - ', $label);
	}
	
	public static function findByCodProjeto($cod_projeto, $id_votacao, $id_regiao) {
		if (!is_array($cod_projeto)) return parent::findByCodProjeto($cod_projeto);
		
		foreach ($cod_projeto as $k => $v)
			$cod_projeto[$k] = PDOUtils::getConn()->quote($v, PDO::PARAM_INT);
		
		$cod_projeto = join(', ', $cod_projeto);
		
		
		$sql = str_replace(':cod_projeto:', $cod_projeto, CedulaQueries::SQL_FIND_BY_COD_PROJ_IN);
		$query = PDOUtils::getConn()->prepare($sql);
		if ($query->execute(compact('id_votacao', 'id_regiao')) === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	public function isExpandable() {
		$descricao = trim($this->getDsDemanda());
		$abrangencia = trim($this->getDsAbrangencia());
		return (strlen($abrangencia) > 0 || strlen($descricao) > 0);
	}
}
