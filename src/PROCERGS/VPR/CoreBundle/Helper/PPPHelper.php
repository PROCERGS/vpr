<?php
namespace PROCERGS\VPR\CoreBundle\Helper;

use PROCERGS\VPR\CoreBundle\Entity\Poll;

class PPPHelper
{
    protected $host;
    protected $port;
    protected $dbname;
    protected $username;
    protected $password;
    
    const COD_ORGAO_SEPLAN = 13;
    
    public function setHost($var)
    {
        $this->host = $var;
    }
    public function getHost()
    {
        return $this->host;
    }
    public function setPort($var)
    {
        $this->port = $var;
    }
    public function getPort()
    {
        return $this->port;
    }
    public function setDbname($var)
    {
        $this->dbname = $var;
    }
    public function getDbname()
    {
        return $this->dbname;
    }
    public function setUsername($var)
    {
        $this->username = $var;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setPassword($var)
    {
        $this->password = $var;
    }
    public function getPassword()
    {
        return $this->password;
    }
    
    private static function oci_execute(&$statement)
    {
        if (!oci_execute($statement, OCI_NO_AUTO_COMMIT)) {
            $e = oci_error();
            throw new \Exception($e['message']);
        }
    }
    
    private $_conn;
    private function createConn()
    {
        $dsn = '(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST='.$this->host.')(PORT='.$this->port.'))(CONNECT_DATA=(SERVICE_NAME='.$this->dbname.')))';
        $this->_conn = oci_connect($this->username, $this->password, $dsn, 'AL32UTF8');
        if (! $this->_conn) {
            $e = oci_error();
            throw new \Exception($e['message']);
        }
        return true;
    }
    
    private function cleanUp(Poll &$poll)
    {
        if ($poll->getPppCodProjeto()) {
            $sql = "delete from pop_demanda_pleito where cod_demanda in (select cod_demanda from pop_demanda where cod_projeto = ". $poll->getPppCodProjeto()." )";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $sql = "delete from pop_demanda where cod_projeto = ". $poll->getPppCodProjeto()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $sql = "delete from pop_descricao_cedula where nro_ano_corrente = ". $poll->getTransferYear()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);

            $sql = "delete from pop_arquivo_corede where nro_ano_corrente = ". $poll->getTransferYear()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);

            $sql = "delete from pop_projeto where cod_projeto = ". $poll->getPppCodProjeto()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);

            $sql = "delete from pop_programa where cod_programa = ". $poll->getPppCodPrograma()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
        }
    }
    
    private function cleanUpDanger(Poll &$poll)
    {
        if ($poll->getPppCodProjeto()) {
            $sql = "delete from POP_CIDADAO where nro_ano_corrente = ". $poll->getTransferYear();
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $sql = "delete from pop_voto_web where nro_ano_corrente = ". $poll->getTransferYear();
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);            

            $sql = "delete from POP_VOTO_MANUAL where nro_ano_corrente = ". $poll->getTransferYear();
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $sql = "delete from pop_demanda_pleito where cod_demanda in (select cod_demanda from pop_demanda where cod_projeto = ". $poll->getPppCodProjeto()." )";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = "delete from pop_demanda where cod_projeto = ". $poll->getPppCodProjeto()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = "delete from pop_descricao_cedula where nro_ano_corrente = ". $poll->getTransferYear()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = "delete from pop_arquivo_corede where nro_ano_corrente = ". $poll->getTransferYear()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = "delete from pop_projeto where cod_projeto = ". $poll->getPppCodProjeto()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = "delete from pop_programa where cod_programa = ". $poll->getPppCodPrograma()." ";
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
        }
    }
    
    public function sync(Poll &$poll,\Doctrine\DBAL\Connection &$conn2)
    {        
        //comeca transaction
        $this->createConn();        
        $conn2->beginTransaction();
        try {
            $this->cleanUp($poll);
            
            $sql = 'insert into pop_programa (cod_programa, nro_programa, txt_programa, cod_orgao, nro_ano_corrente) values (';
            $sql .= '(select nvl(max(cod_programa), 0) +1 from pop_programa), (select nvl(max(cod_programa), 0) +1 from pop_programa), ';
            $sql .= "'Consulta Popular ". $poll->getTransferYear() ."', ". PPPHelper::COD_ORGAO_SEPLAN.", " . $poll->getTransferYear() . " )";        
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
    
            $sql = 'select max(cod_programa) cod_programa from pop_programa ';
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $pop_programa = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
            $codPrograma = $pop_programa['COD_PROGRAMA'];
            
            $sql = 'insert into pop_projeto (cod_projeto, nro_projeto, txt_projeto, cod_programa, nro_ano_corrente) values (';
            $sql .= '(select nvl(max(cod_projeto), 0) +1 from pop_projeto), (select nvl(max(cod_projeto), 0) +1 from pop_projeto), ';
            $sql .= "'Consulta Popular ". $poll->getTransferYear() ."', ".$codPrograma.', ' . $poll->getTransferYear() . ' )';
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $sql = 'select max(cod_projeto) cod_projeto from pop_projeto ';
            $stid = oci_parse($this->_conn, $sql);
            self::oci_execute($stid);
            
            $pop_programa = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
            $codProjeto = $pop_programa['COD_PROJETO'];
            
            $sql = 'insert into pop_descricao_cedula (cod_desc_cedula, txt_desc_cedula, nro_ordem, cod_corede, nro_ano_corrente, ctr_dth_inc, ctr_usu_inc, ctr_dth_atu, ctr_usu_atu) values (';
            $sql .= 'pop_seq_cod_desc_cedula.nextval, :txt_desc_cedula,:nro_ordem,:cod_corede, '. $poll->getTransferYear().', sysdate,999999, null,null) ';
            $stid = oci_parse($this->_conn, $sql);
            
            $sql = 'select max(cod_desc_cedula) cod_desc_cedula from pop_descricao_cedula';        
            $stid2 = oci_parse($this->_conn, $sql);
            
            $sql = 'insert into pop_demanda (cod_demanda, txt_demanda, cod_projeto, txt_unidade_medida, vlr_unitario, txt_requisito, tp_servico_invest, ind_regional, nro_ano_corrente, nro_uo, txt_uo, nro_demanda, txt_produto, nro_natureza_despesa, nro_controle_temp) values (';
            $sql .= "pop_seq_cod_desc_cedula.nextval, :txt_demanda, ". $codProjeto.", 'Unidade', :vlr_unitario, null, null, 1, ".$poll->getTransferYear().", null, null, 1, null, null, :nro_controle_temp) ";
            $stid3 = oci_parse($this->_conn, $sql);
            
            $sql = 'select max(cod_demanda) cod_demanda from pop_demanda';
            $stid4 = oci_parse($this->_conn, $sql);
            
            $sql = 'insert into pop_demanda_pleito (cod_demanda_pleito, cod_demanda, txt_localizacao, cod_desc_cedula, nro_unidades, cod_corede, cod_municipio, nro_ano_corrente, nro_votos_comude, ctr_dth_inc, ctr_usu_inc, ctr_dth_atu, ctr_usu_atu, cod_orgao, cod_localizacao) values (';
            $sql .= "pop_seq_cod_demanda_pleito.nextval, :cod_demanda, 'Estadual', :cod_desc_cedula, 1, :cod_corede, 326, ".$poll->getTransferYear().", 0, sysdate, 999999, null, null, ".PPPHelper::COD_ORGAO_SEPLAN.", 0) ";
            $stid5 = oci_parse($this->_conn, $sql);
            
            $sql = 'insert into pop_arquivo_corede (cod_arquivo, txt_arquivo, cod_corede, ctr_dth_inc, ctr_usu_inc, nro_ano_corrente, ctr_dth_homologacao, ctr_usu_homologacao, ind_carregado, txt_log_carga) values (';
            $sql .= "pop_seq_cod_arquivo.nextval, 'Process espec ".$poll->getTransferYear()."', :corede_id, sysdate, 999999, ".$poll->getTransferYear().", sysdate, 999999, 1, null) ";
            $stid6 = oci_parse($this->_conn, $sql);        
            
            $sql = "update poll_option set cod_desc_cedula = ? where id = ?";
            $stmt3 = $conn2->prepare($sql);        
            
            $sql = "select a1.id, a3.name, a1.title, a1.cost, a1.category_sorting, a1.corede_id
    from poll_option a1
    inner join step a2 on a1.step_id = a2.id
    inner join category a3 on a1.category_id = a3.id
    where a2.poll_id = " . $poll->getId();
            
            $stmt2 = $conn2->prepare($sql);
            $stmt2->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt2->execute();
            
            $coredes = array();
            while ($result = $stmt2->fetch()) {
                $txt = $result['name'] . ' - ' . $result['title'];
                if ($result['cost']) {
                    $txt .= " Valor: R$ ". number_format($result['cost'],2,",","."); 
                }
                $txt2 = substr($txt, 0, 300);
                oci_bind_by_name($stid, ":txt_desc_cedula", $txt2, -1, SQLT_CHR);
                oci_bind_by_name($stid, ":nro_ordem", $result['category_sorting'], -1, OCI_B_INT);
                oci_bind_by_name($stid, ":cod_corede", $result['corede_id'], -1, OCI_B_INT);
                self::oci_execute($stid);
                
                self::oci_execute($stid2);            
                $pop_descricao_cedula = oci_fetch_array($stid2, OCI_ASSOC + OCI_RETURN_NULLS);            
                $cod_desc_cedula = $pop_descricao_cedula['COD_DESC_CEDULA'];
                
                oci_bind_by_name($stid3, ":txt_demanda", $txt, -1, SQLT_CHR);
                if (null === $result['cost']) {
                    $cost = 0;
                } else {
                    $cost = $result['cost']*1;
                }
                oci_bind_by_name($stid3, ":vlr_unitario", $cost, -1, OCI_B_INT);
                $controleTemp = ($result['corede_id'] * 100) + ($result['category_sorting']*1);
                oci_bind_by_name($stid3, ":nro_controle_temp", $controleTemp, -1, OCI_B_INT);
                self::oci_execute($stid3, array($txt,$cost,$controleTemp));
                
                self::oci_execute($stid4);
                $pop_demanda = oci_fetch_array($stid4, OCI_ASSOC + OCI_RETURN_NULLS);
                $cod_demanda = $pop_demanda['COD_DEMANDA'];
                
                oci_bind_by_name($stid5, ":cod_demanda", $cod_demanda);
                oci_bind_by_name($stid5, ":cod_desc_cedula", $cod_desc_cedula);
                oci_bind_by_name($stid5, ":cod_corede", $result['corede_id']);
                
                self::oci_execute($stid5);
                            
                $stmt3->execute(array($cod_desc_cedula, $result['id']));
                if (!isset($coredes[$result['corede_id']])) {
                    $coredes[$result['corede_id']] = 1;
                }
            }
            foreach ($coredes as $coredeId => $val) {
                oci_bind_by_name($stid6, ":corede_id", $coredeId);
                self::oci_execute($stid6);
            }
            $conn2->commit();
            oci_commit($this->_conn);
            $poll->setPppCodPrograma($codPrograma);
            $poll->setPppCodProjeto($codProjeto);
            return true;
        } catch (\Exception $e) {
            oci_rollback($this->_conn);
            $conn2->rollBack();       
        }
        return false;
    }
}
