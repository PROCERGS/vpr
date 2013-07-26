<?php

class Stats extends AppController
{

    public static function by_region()
    {
        $cache = self::getParam('cache');

        self::setPageName("Acompanhamento da Votação");
        self::setPageSubName("Parciais atualizadas por tipo de mídia online");

        $values = CacheFS::get('statsSummary');

        if (is_null($values) || $cache == 'off') {
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

            $date = new DateTime();

            $values = compact('meios_votacao', 'eleitores', 'votos', 'date');

            CacheFS::set('statsSummary', $values);
            $values = CacheFS::get('statsSummary');
        }

        self::render($values);
    }

    public static function summary()
    {
        self::setPageName("Acompanhamento da Votação");
        self::setPageSubName("Parciais atualizadas por tipo de mídia online");

        $votacao = Votacao::findMostRecent();
        
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
        
        // Poll data
        $polls = Stat::findPollsByVotacaoId($votacao->getIdVotacao());

        $values = compact('totalVotos', 'totalVotosByMeioVotacao', 'totalCidadaos', 'totalCidadaosByMeioVotacao', 'meios_votacao', 'polls');

        self::render($values);
    }

}