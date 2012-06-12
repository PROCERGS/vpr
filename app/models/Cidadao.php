<?php
class Cidadao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_cidadao;
	protected $nro_titulo;
	protected $rg;
	protected $ds_email;
	protected $nro_telefone;

	/**
	 * @return Cidadao
	 */
	public static function cast($o) { return $o; }
	
	public static function auth($nro_titulo, $rg) {
		$cidadao = Cidadao::findByNroTituloOrRg($nro_titulo, $rg);
		if (count($cidadao) <= 0) {
			$eleitor_tre = reset(EleitorTre::findByNroTitulo($nro_titulo));
			if ($eleitor_tre instanceof EleitorTre) {
				$cidadao = new Cidadao();
				$cidadao->setNroTitulo($eleitor_tre->getNroTitulo());
				$cidadao->setRg($rg);
				$cidadao->insert();
			}
		} elseif (count($cidadao) == 1) {
			$cidadao = reset($cidadao);
			if ($cidadao->getRg() != $rg || $cidadao->getNroTitulo() != $nro_titulo)
				throw new DocumentsMismatchException();
		} elseif (count($cidadao) > 1) {
			throw new DocumentsMismatchException();
		} else {
			throw new ErrorException('Unknown error.');
		}
		
		if ($cidadao instanceof Cidadao)
			return $cidadao;
		
		return NULL;
	}
	
	public static function findByNroTituloOrRg($nro_titulo, $rg) {
		$query = PDOUtils::getConn()->prepare(CidadaoQueries::SQL_FIND_BY_NRO_TITULO_OR_RG);
		$query->bindValue('nro_titulo', $nro_titulo);
		$query->bindValue('rg', $rg);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	// TODO: implementar também em javascript.
	public static function validateRG_RS($rg) {
		$rg = (string) $rg;
		// Cálculo do DCE
		$dce = 0;
		for ($i = 1; $i < 9; $i++) {
			$dce += $rg[$i] * ($i+1);
		}
		$dce = $dce%11;
		if ($dce <= 1) $dce = 1;
		else $dce = 11 - $dce;
		
		// Cálculo do DCD
		$dcd = 0;
		for ($i = 0; $i <= 8; $i += 2) {
			$dcd += ($rg[$i] * 2) % 9;
			
			if ($i >= 8) continue;
			$dcd += $rg[$i+1];
		}
		
		for ($i = 0; $i <= 8; $i += 2) {
			if ($rg[$i] == 9)
				$dcd += 9;
		}
		
		$dcd = $dcd % 10;
		if ($dcd == 0) $dcd = 1;
		else $dcd = 10 - $dcd;
		
		return ($dce == $rg[0] && $dcd == $rg[strlen($rg) - 1]);
	}
}
