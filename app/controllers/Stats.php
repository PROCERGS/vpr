<?php
class Stats extends AppController {
	public static function summary() {
		self::setPageName("Acompanhamento da Votação");
		self::setPageSubName("Parciais atualizadas por tipo de mídia online");
		
		$totalVotos = reset(Stat::findByQtdVotos());
		$totalVotosByMeioVotacaoRaw = Stat::findByQtdVotosByMeioVotacao();
		
		$totalCidadaos = reset(Stat::findByQtdCidadaos());
		$totalCidadaosByMeioVotacaoRaw = Stat::findByQtdCidadaosByMeioVotacao();
		
		$totalVotosByMeioVotacao = array();
		$totalCidadaosByMeioVotacao = array();
		$meios_votacao = array();
		foreach ($totalVotosByMeioVotacaoRaw as $stat) {
			$meios_votacao[$stat['nm_meio_votacao']] = 1;
			$totalVotosByMeioVotacao[$stat['nm_meio_votacao']] = $stat['total'];
		}
		foreach ($totalCidadaosByMeioVotacaoRaw as $stat) {
			$meios_votacao[$stat['nm_meio_votacao']] = 1;
			$totalCidadaosByMeioVotacao[$stat['nm_meio_votacao']] = $stat['total'];
		}
		$meios_votacao = array_keys($meios_votacao);
		
		$values = compact('totalVotos', 'totalVotosByMeioVotacao', 'totalCidadaos', 'totalCidadaosByMeioVotacao', 'meios_votacao');
		
		self::render($values);
	}
}