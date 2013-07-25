<?php

class VotingSession extends Model
{

    protected $current_user;
    protected $current_group;
    protected $voto_log;
    protected $votes;
    protected $options;
    protected $captcha;

    /**
     * 
     * @param Cidadao $currentUser
     */
    public function __construct($currentUser)
    {
        if ($currentUser instanceof Cidadao)
            $this->setCurrentUser($currentUser);
        else
            throw new ErrorException("Invalid Cidadao.");
    }

    /**
     * @return VotingSession
     */
    public static function getCurrentVotingSession()
    {
        $votingSession = Session::get('currentVotingSession');
        if (!($votingSession instanceof VotingSession))
            return NULL;
        else
            return $votingSession;
    }

    /**
     * @return VotingSession
     */
    public static function requireCurrentVotingSession()
    {
        if (!(self::getCurrentVotingSession() instanceof VotingSession)) {
            //$currentSession = new VotingSession();
            //$currentSession->save();
            return AppController::redirect(array('controller' => 'Auth', 'action' => 'login'));
        }
        return Session::get('currentVotingSession');
    }

    public function save()
    {
        Session::set('currentVotingSession', $this);
    }

    public function destroy()
    {
        Session::delete('currentVotingSession');
    }

    public function resetVotes()
    {
        $this->votes = NULL;
        $this->save();
    }

    public function setCurrentGroup($group)
    {
        $this->current_group = $group;
        $this->save();
    }

    /**
     * @return GrupoDemanda
     */
    public function getCurrentGroup()
    {
        if (is_null($this->current_group)) {
            $group = GrupoDemanda::findNextAvailableGroup();
            if (count($group) > 0)
                $this->setCurrentGroup(reset($group));
            else
                throw new AppException("Nenhum item disponível para votação.", AppException::WARNING);
        }
        return $this->current_group;
    }

    public function requireCurrentUser()
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser instanceof Cidadao) {
            return $currentUser;
        } else
            AppController::redirect(array('controller' => 'Auth', 'action' => 'login'));
    }

    public function setCurrentUser($currentUser)
    {
        if (!is_null($currentUser) && !($currentUser->getEleitorTre() instanceof EleitorTre))
            $currentUser->fetchEleitorTre();
        $this->current_user = $currentUser;
        $this->save();
    }

    public function setVotoLog($votoLog)
    {
        $this->voto_log = $votoLog;
        $this->save();
    }

    public function setVotes($votes)
    {
        $this->votes = $votes;
        $this->save();
    }

    public function setOptions($id_grupo_demanda, $options)
    {
        $this->options[$id_grupo_demanda] = $options;
        $this->save();
    }

    public function getOptions($id_grupo_demanda)
    {
        if (!is_array($this->options) || is_null($this->options) || !array_key_exists($id_grupo_demanda, $this->options)) {
            $id_regiao = $this->getCurrentUser()->getRegiao()->getIdRegiao();
            $options = $this->getCurrentGroup()->getOptions($id_regiao);
            $this->setOptions($id_grupo_demanda, $options);
        }
        return $this->options[$id_grupo_demanda];
    }

    public function getStep($step)
    {
        $options = $this->getOptions($this->getCurrentGroup()->getIdGrupoDemanda());
        $pageSize = Config::get('votes.pageSize');
        $pages = array_chunk($options, $pageSize);

        if ($step > 0 && $step <= count($pages)) {
            return array(
                'content' => $pages[$step - 1],
                'pages' => count($pages),
                'pageSize' => $pageSize
            );
        }
    }

    public function finishGroup()
    {
        $this->setVotoLog(NULL);
        $this->setVotes(NULL);
        $this->setCurrentGroup(NULL);
        $this->save();
    }

    public function finish()
    {
        $this->setVotoLog(NULL);
        $this->setVotes(NULL);
        $this->setCurrentGroup(NULL);
        $this->setCurrentUser(NULL);
        $this->save();
        $this->destroy();
    }

    public function getLastStep()
    {
        $first = $this->getStep(1);
        return $first['pages'];
    }

    public function setCaptcha($captcha)
    {
        $this->captcha = $captcha;
        $this->save();
    }

    public function getVotacaoId()
    {
        return $this->getCurrentGroup()->getIdVotacao();
    }

}
