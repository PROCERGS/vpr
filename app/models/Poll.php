<?php

class Poll extends Model
{

    protected static $__schema = 'seplag_vpr';
    protected $id;
    protected $votacao_id;
    protected $title;

    /**
     * @return Poll
     */
    public static function cast($o)
    {
        return $o;
    }

    /**
     * Retorna a última enquete da votação
     * 
     * @return Poll
     */
    public static function findLastByVotacao($votacao_id)
    {
        $query = PDOUtils::getConn()->prepare(PollQueries::SQL_FIND_LAST);
        $query->bindValue('votacao_id', $votacao_id);
        if ($query->execute() === TRUE) {
            $result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
            return reset($result);
        } else
            return null;
    }

    /**
     * Retorna a última enquete não votada da votação
     * 
     * @return Poll
     */
    public static function findLastUnvotedByVotacao($votacao_id, $cidadao_id)
    {
        $query = PDOUtils::getConn()->prepare(PollQueries::SQL_FIND_LAST_UNVOTED_BY_VOTACAO);
        if ($query->execute(compact('votacao_id', 'cidadao_id')) === TRUE) {
            $result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
            if (!empty($result)) {
                return reset($result);
            } else {
                return null;
            }
        } else
            return null;
    }

    /**
     * Retorna todas as questões da enquete
     * @return PollQuestion[]
     */
    public function getQuestions()
    {
        $query = PDOUtils::getConn()->prepare(PollQueries::SQL_GET_QUESTIONS);
        $poll_id = $this->getId();
        $query->execute(compact('poll_id'));

        return $query->fetchAll(PDO::FETCH_CLASS, 'PollQuestion');
    }

}
