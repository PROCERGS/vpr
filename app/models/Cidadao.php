<?php
class Cidadao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_cidadao;
	protected $nro_titulo;
	protected $rg;
	protected $ds_email;
	protected $nro_telefone;
	protected $id_municipio;
	private $eleitor_tre;
	private $regiao;

	/**
	 * @return Cidadao
	 */
	public static function cast($o) { return $o; }
	
	public static function auth($nro_titulo, $rg) {
		$previous = array('controller' => 'Auth', 'action' => 'login');
		
		$nro_titulo = preg_replace("/([^0-9])/", '', $nro_titulo);
		$nro_titulo = trim($nro_titulo);
		$nro_titulo = str_pad($nro_titulo, 12, '0', STR_PAD_LEFT);
		
		$rg = trim($rg);
		$rg = str_pad($rg, 10, '0', STR_PAD_LEFT);
		
		if (!self::validateRG_RS($rg))
			throw new AppException("RG inválido.", AppException::ERROR, $previous);
		
		$cidadao = Cidadao::findByNroTituloOrRg($nro_titulo, $rg);
		if (count($cidadao) <= 0) {
			$eleitor_tre = reset(EleitorTre::findByNroTitulo($nro_titulo));
			if ($eleitor_tre instanceof EleitorTre) {
				$cidadao = new Cidadao();
				$cidadao->setEleitorTre($eleitor_tre);
				$cidadao->fetchRegiao();
				$cidadao->setNroTitulo($eleitor_tre->getNroTitulo());
				$cidadao->setRg($rg);
				
				$municipio = reset(Municipio::findByCodMunTre($eleitor_tre->getCodMunTre()));
				if ($municipio instanceof Municipio)
					$cidadao->setIdMunicipio($municipio->getIdMunicipio());
				else
					throw new AppException("Município não encontrado.", AppException::ERROR, $previous);
				
				$cidadao->setIdCidadao($cidadao->insert());
			} else
				throw new AppException("Eleitor não encontrado.", AppException::ERROR, $previous);
		} elseif (count($cidadao) == 1) {
			$cidadao = Cidadao::cast(reset($cidadao));
			$cidadao->fetchEleitorTre();
			$cidadao->fetchRegiao();
			
			if ($cidadao->getRg() != $rg)
				throw new DocumentsMismatchException('Esse título de eleitor já foi usado nessa votação.', $previous);
			if ($cidadao->getNroTitulo() != $nro_titulo)
				throw new DocumentsMismatchException('Esse RG já foi usado nessa votação', $previous);
		} elseif (count($cidadao) > 1) {
			throw new DocumentsMismatchException('Um dos documentos parece pertencer a outra pessoa. Verifique seus dados.', $previous);
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
		$rg = str_pad($rg, 10, '0', STR_PAD_LEFT);
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
	
	public function setEleitorTre($eleitor_tre) { $this->eleitor_tre = $eleitor_tre; }
	public function getEleitorTre() { return $this->eleitor_tre; }
	public function fetchEleitorTre() {
		$eleitor_tre = $this->getEleitorTre();
		if ($eleitor_tre instanceof EleitorTre)
			return $eleitor_tre;
		
		$eleitor_tre = reset(EleitorTre::findByNroTitulo($this->getNroTitulo()));
		$this->setEleitorTre($eleitor_tre);
		return $eleitor_tre;
	}
	
	public function setRegiao($regiao) { $this->regiao = $regiao; }
	public function getRegiao() { return $this->regiao; }
	public function fetchRegiao() {
		$regiao = $this->getRegiao();
		if ($regiao instanceof Regiao)
			return $regiao;
	
		$regiao = reset(Regiao::findByCodMunTre($this->getEleitorTre()->getCodMunTre()));
		$this->setRegiao($regiao);
		return $regiao;
	}
	
	public function voted($id_votacao) {
		$voto_logs = VotoLog::findByIdCidadaoIdVotacaoCompleted(array(
					'id_cidadao' => $this->getIdCidadao(),
					'id_votacao' => $id_votacao
				));
		return (is_array($voto_logs) && count($voto_logs) > 0);
	}
}
