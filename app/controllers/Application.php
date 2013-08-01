<?php

class Application extends AppController
{

    public static function index()
    {
        $votacao = Votacao::findMostRecent();
        self::setPageName("Votação de Prioridades<br />Orçamento " . $votacao->getIntExercicio());

        $string_alt_img = null;

        $html = new HTMLHelper();

        $show_vote_now = FALSE;
        if ($votacao instanceof Votacao) {
            $data_ini = $votacao->getDthInicio();
            $data_fim = $votacao->getDthFim();

            $data_ini = Util::createDateTime($votacao->getDthInicio(), 'Y-m-d H:i:s');
            $data_fim = Util::createDateTime($votacao->getDthFim(), 'Y-m-d H:i:s');
            $string_data = self::stringDate($data_ini, $data_fim);

            $show_vote_now = $votacao->isOpen();

            $nome = $votacao->getNmVotacao();
            $orçamento = $votacao->getBudgetYear();
            if (is_string($nome) && is_string($orçamento)) {
                $string_alt_img = $nome + " - " + $orçamento;
                //$show_vote_now = FALSE;
            }
        }

        if (Config::get('isMobile')) {
            self::render(compact("string_data", "string_alt_img", "show_vote_now"), array('controller' => 'Application', 'action' => 'index_mobile'));
        } else {
            self::render(compact("string_data", "string_alt_img", "show_vote_now"));
        }
    }

    public static function make_class()
    {
        $className = self::getParam('class');
        $tableName = self::getParam('table');

        if (is_null($className))
            $className = Util::parseControllerName($tableName);

        $class = ClassMaker::createClass($className, $tableName);
        $class_name = ClassMaker::getClassName();
        $class_filename = ClassMaker::getClassFileName();

        if (self::getParam('save') == 'true') {
            $fullpath = MODELS_PATH . $class_filename;
            file_put_contents($fullpath, $class);

            header("Content-type: text/plain");
            echo file_get_contents($fullpath);
            return;
        }

        $tables = ClassMaker::getTables();
        self::render(compact('class', 'class_name', 'class_filename', 'tableName', 'tables'));
    }

    private static function stringDate($data_ini, $data_fim)
    {
        setlocale(LC_ALL, "pt_BR", "pt_BR.utf-8", "pt_BR.iso-8859-1", "portuguese", "ptb");
        $now = new DateTime();
        $current_mk = time();

        /**
         * 	Caso queira utilizar um formato padrão de data 
         * 	descomente as linhas de baixo
         */
//		$string_data = "Período de Votação Online: dia 4 de julho de 2012 - das 8 às 24h";
        $from = strftime("das %Hh do dia %d/%m/%Y", $data_ini->getTimestamp());
        $to = strftime("até as %H:%i do dia %d/%m/%Y", $data_fim->getTimestamp());
        $string_data = "Votação Online: $from $to";

        return $string_data;
    }

    public static function pesquisa()
    {
        self::render();
    }

}
