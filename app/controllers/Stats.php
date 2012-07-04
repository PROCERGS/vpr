<?php
class Stats extends AppController {
	public static function summary() {
		self::setPageName("Acompanhamento da Votação");
		self::setPageSubName("Parciais atualizadas por tipo de mídia online");
		
		$meios_votacao = array();
		
		$eleitores = array('totais' => array());
		$votos = array('totais' => array());
		
		$cidadaosPorRegiaoMeioVotacao = Stat::findCidadaosPorRegiaoMeioVotacao();
		foreach ($cidadaosPorRegiaoMeioVotacao as $stat) {
			$meio = $stat['nm_meio_votacao'];
			$regiao = $stat['nm_regiao'];
			$total = $stat['total'];
			
			$meios_votacao[$meio] = 1;
			@$eleitores[$regiao][$meio] = $total;
			@$eleitores[$regiao]['total'] += $total;
			@$eleitores['totais'][$meio] += $total;
		}
		
		$votosPorRegiaoMeioVotacao = Stat::findVotosPorRegiaoMeioVotacao();
		foreach ($votosPorRegiaoMeioVotacao as $stat) {
			$meio = $stat['nm_meio_votacao'];
			$regiao = $stat['nm_regiao'];
			$total = $stat['total'];
			
			$meios_votacao[$meio] = 1;
			@$votos[$regiao][$meio] = $total;
			@$votos[$regiao]['total'] += $total;
			@$votos['totais'][$meio] += $total;
		}
		$meios_votacao = array_keys($meios_votacao);
		
		$values = compact('meios_votacao', 'eleitores', 'votos');
		
		self::render($values);
	}
}