<?php
namespace PROCERGS\VPR\CoreBundle\Helper;

class SMSHelper
{

    protected $url;
    protected $sistema;
    protected $ordemServico;
    protected $senha;
    
    protected $aplicacao;
    protected $ddd;
    protected $telefone;
    protected $remetente;
    protected $mensagem;
    protected $hmIniEnvio;
    protected $hmFinEnvio;
    
    private $cookie;

    public function setUrl($var)
    {
        $this->url = $var;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setSistema($var)
    {
        $this->sistema = $var;
    }
    
    public function getSistema()
    {
        return $this->sistema;
    }
    
    public function setOrdemServico($var)
    {
        $this->ordemServico = $var;
    }
    
    public function getOrdemServico()
    {
        return $this->ordemServico;
    }
    
    public function setSenha($var)
    {
        $this->senha = $var;
    }
    
    public function getSenha()
    {
        return $this->senha;
    }
    
    public function setAplicacao($var)
    {
        $this->aplicacao = $var;
    }
    
    public function getAplicacao()
    {
        return $this->aplicacao;
    }
    
    public function setDdd($var)
    {
        $this->ddd = $var;
    }
    
    public function getDdd()
    {
        return $this->ddd;
    }
    
    public function setTelefone($var)
    {
        $this->telefone = $var;
    }
    
    public function getTelefone()
    {
        return $this->telefone;
    }
    
    public function setRemetente($var)
    {
        $this->remetente = $var;
    }
    
    public function getRemetente()
    {
        return $this->remetente;
    }
    
    public function setMensagem($var)
    {
        $this->mensagem = $var;
    }
    
    public function getMensagem()
    {
        return $this->mensagem;
    }
    
    public function setHmIniEnvio($var)
    {
        $this->hmIniEnvio = $var;
    }
    
    public function getHmIniEnvio()
    {
        return $this->hmIniEnvio;
    }
    
    public function setHmFinEnvio($var)
    {
        $this->hmFinEnvio = $var;
    }
    
    public function getHmFinEnvio()
    {
        return $this->hmFinEnvio;
    }
    
    private function _common($header = array())
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        $headApp = array(
            'Host: webgenteste',
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',
            'Referer: http://webgenteste/torpedos/tpd-torpedo-env_web.html',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded'
        );
        $headApp = array_merge($headApp, $header);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headApp);
        curl_setopt($this->ch, CURLOPT_ENCODING, '');
        if (! $this->cookie) {
            $this->cookie = tempnam(SMSHelper::sys_get_temp_dir(), "sms");
        }
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie);
    }
    
    public static function sys_get_temp_dir()
    {
        $tmp = getenv('TMPDIR');
        if ($tmp && @is_writable($tmp)) {
            return $tmp;
        } elseif (function_exists('sys_get_temp_dir') && @is_writable(sys_get_temp_dir())) {
            return sys_get_temp_dir();
        }
        return ini_get('upload_tmp_dir');
    }

    public function send()
    {
        
        if (
            !$this->sistema ||
            !$this->aplicacao ||
            !$this->ordemServico ||
            !$this->aplicacao ||
            !$this->ddd ||
            !$this->telefone ||
            !$this->remetente ||
            !$this->mensagem ||
            !$this->senha 
            ) {
            throw new \Exception("Faltou parametro obrigatorio");
        }
        $data = http_build_query(array(
            'TR' => 'tpd-torpedo-env_web',
            'A4-VERSAO' => 'W001',
            'A3-Sist' => $this->sistema,
            'A50-Aplic' => $this->aplicacao,
            'N6-OS' => $this->ordemServico,
            'A50-Aplic' => $this->aplicacao,
            'A2-DDD' => $this->ddd,
            'N9-Telef' => $this->telefone,
            'A10-Remet' => $this->remetente,
            'A139-MsgSMS' => $this->mensagem,
            'N4-HM-Ini-Envio' => $this->hmIniEnvio === null ? '' : $this->hmIniEnvio,
            'N4-HM-Fin-Envio' => $this->hmFinEnvio === null ? '' : $this->hmFinEnvio,
            'A8-Senha' => $this->senha
        ));
        $this->_common(array('Content-Length: ' . strlen($data)));
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        // curl_setopt($this->ch, CURLOPT_NOBODY, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        $result = curl_exec($this->ch);
        curl_close($this->ch);
        if (!preg_match('/TPD encaminhado - protocolo=([0-9]{1,})/', $result, $matches)) {
            throw new \Exception('Ocorreu um erro no envio do sms');
        }
        return $matches[1];
    }

    public function __destruct()
    {
        @unlink($this->cookie);
    }
}
