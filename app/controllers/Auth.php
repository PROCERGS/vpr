<?php

class Auth extends AppController
{

    public static function login()
    {
        self::addJavascript('/js/login.js');

        $votacao = Votacao::findMostRecent();
        self::setPageName("Votação de Prioridades - Orçamento " . $votacao->$votacao->getBudgetYear());
        if ($_POST) {
            $previous = array('controller' => 'Auth', 'action' => 'login');
            try {
                $votacao = Votacao::findByActiveVotacao();
                if (count($votacao) == 0)
                    throw new AppException("A votação não está aberta.", AppException::INFO, $previous);

                $doc = self::getParam('doc');
                $cidadao = self::getParam('Cidadao');
                $cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
                $phone = $cidadao->getNroTelefone();
                $email = $cidadao->getDsEmail();

                if (strlen($cidadao->getNroTitulo()) <= 0 || strlen($doc) <= 0)
                    throw new AppException("Informe o Título de Eleitor e o documento de identificação (RG ou CPF)", AppException::ERROR, $previous);

                $cidadao = Cidadao::auth($cidadao->getNroTitulo(), $doc);

                if (strlen($phone) > 0)
                    $cidadao->setNroTelefone($phone);
                if (strlen($email) > 0)
                    $cidadao->setDsEmail($email);

                if (strlen($phone) > 0 || strlen($email) > 0)
                    $cidadao->update();
            } catch (AppException $e) {
                if (!isset($cidadao) || !($cidadao instanceof Cidadao))
                    $cidadao = new Cidadao();

                $extra = array(
                    'titulo' => $cidadao->getNroTitulo(),
                    'rg' => $cidadao->getRg(),
                    'celular' => $cidadao->getNroTelefone(),
                    'email' => $cidadao->getDsEmail(),
                    'cidadao' => $cidadao
                );
                $e->setExtra($extra);
                throw $e;
            }

            if ($cidadao instanceof Cidadao) {
                $votingSession = new VotingSession($cidadao);
                $votingSession->save();
                self::redirect(array('controller' => 'Election', 'action' => 'start'));
            } else
                echo "Acesso negado!";
        }
        self::render(compact('html'));
    }

    public static function logout()
    {
        Session::delete('currentUser');
        Session::destroy();
    }

}
