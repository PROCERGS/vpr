<?php

class Cedulas extends AppController
{

    public static function before()
    {
        parent::before();
        $votacao = Votacao::findCachedMostRecent();
        self::setPageName("Consulta de Cédulas de Votação");
        self::setPagesubName("Votação de Prioridades - Orçamento " . $votacao->getBudgetYear());
    }

    public static function index()
    {

        self::setTitle("Cédulas por Região");

        $html = new HTMLHelper();

        $grupo_urnas = Urna::findByTxtLocalizacaoAgrup();
        $array = array();
        foreach ($grupo_urnas as $urna) {
            array_push($array, $urna->getNmMunicipio());
        }
        $string_array = implode("|", $array);

        self::addCSS('/css/jquery-ui-1.8.21.custom.css');
        self::addJavascript('/js/jquery-1.7.2.min.js');
        self::addJavascript('/js/jquery-ui-1.8.21.custom.min.js');
        self::addJavascript('/js/jquery.ui.autocomplete.js');
        self::addJavascript('/js/cedulas.js');

        self::setJavascriptVar('municipiosSearchURL', $html->url(array('controller' => 'Municipios', 'action' => 'search_id_regiao')));

        $options_regiao = Regiao::findAll(array('nm_regiao ASC'));
        $regiao_attr = array("class" => "regiao", 'name' => 'regiao_id', 'id' => 'regiao_id');
        $regiao_settings = array('default_option' => '--------------', 'id_name' => 'id_regiao', 'label_name' => 'nm_regiao');
        $default = -1;

        $select_regiao = $html->select($options_regiao, $default, $regiao_settings, $regiao_attr);

        self::render(compact("select_regiao"));
    }

    public static function consultar()
    {

        self::setTitle("Consulta Cédula de Votação de Prioridades - Orçamento");
        self::addCSS('/css/cedulas.css');

        $regiao_id = -1;
        if (self::getParam('regiao_id'))
            $regiao_id = self::getParam('regiao_id');

        if (isset($regiao_id) && $regiao_id != -1) {

            $votacao = Votacao::findCachedMostRecent();
            self::setTitle("Consulta Cédula de Votação de Prioridades - Orçamento " . $votacao->getBudgetYear());

            if (!empty($votacao)) {
                $votacao = Votacao::cast($votacao);
                $grupos = $votacao->findGruposDemanda();
                $areas = $votacao->findAreasTematicas($regiao_id);

                $groups = array();
                foreach ($grupos as $grupo) {
                    $groups[$grupo->getIdGrupoDemanda()] = array(
                        'group' => $grupo,
                        'areasTematicas' => $grupo->getAreasTematicas($regiao_id)
                    );

                    if ($grupo->getFgTituloSimples() != 1)
                        $groups[$grupo->getIdGrupoDemanda()]['areas'] = $grupo->getOptionsGroupByAreaTematica($regiao_id);
                    else
                        $groups[$grupo->getIdGrupoDemanda()]['options'] = $grupo->getOptions($regiao_id);
                }
            }

            AppController::setRegiao($regiao_id);
        } else
            throw new AppException("Informe a região ou escreva um município correspondente.", AppException::ERROR, array('controller' => 'Cedulas', 'action' => 'index'));

        $readonly = TRUE;

        $poll = Poll::findLastByVotacao($votacao->getIdVotacao());
        
        self::render(compact('groups', 'readonly', 'poll'), array('controller' => 'Election', 'action' => 'step'));
    }

    public static function regiao_municipio()
    {
        $nome_municipio = self::getParam('nome_municipio');
        $municipios = Municipio::findByNmMunicipio($nome_municipio);
        $municipios = reset($municipios);
        $mun = $municipios->getIdRegiao();
        self::render(compact('mun'));
    }

}