<?php
namespace PROCERGS\VPR\CoreBundle\Helper;

class PPPHelper
{
    protected $host = 'pr01.oracle-des.procergs.reders';
    protected $port = '1525';
    protected $dbname = 'pr01';
    protected $username = 'D_SEPLAN_PPP_AD';
    protected $password = 'd353nvppp4d';
    
    public function __construct()
    {
        $this->host = 'pr01.oracle-des.procergs.reders';
        $this->port = '1525';
        $this->dbname = 'pr01';
        $this->username = 'D_SEPLAN_PPP_AD';
        $this->password = 'd353nvppp4d';
    }
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
    
    public function sync()
    {
        $dsn = '(DESCRIPTION=(ADDRESS=(PROTOCOL=tcp)(HOST='.$this->host.')(PORT='.$this->port.'))(CONNECT_DATA=(SERVICE_NAME='.$this->dbname.')))';
        $conn = oci_connect($this->username, $this->password, $dsn, 'AL32UTF8');
        if (! $conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        oci_execute($conn, OCI_NO_AUTO_COMMIT);
        $stid = oci_parse($conn, 'SELECT * FROM pop_descricao_cedula where nro_ano_corrente = 2015');
        oci_execute($stid);
        
        echo "<table border='1'>\n";
        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            echo "<tr>\n";
            foreach ($row as $item) {
                echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
        oci_commit($conn);
        die();
    }
}
