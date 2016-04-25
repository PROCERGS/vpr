<?php

namespace PROCERGS\VPR\CoreBundle\Soe;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

class SoeUserProvider implements UserProviderInterface {
	protected $soe_sigla_sistema;
	protected $soe_url_servicos;
	public function __construct($soe_sigla_sistema, $soe_url_servicos) {
		$this->soe_sigla_sistema = $soe_sigla_sistema;
		$this->soe_url_servicos = $soe_url_servicos;
	}
	public function loadUserByUsername($username) {
		$person = new SoeUser ();
		$person->fromPostArray ( $_POST );
		
		$cod_erro = $_POST ["COD_ERRO"];
		$mensagem = $_POST ["MENSAGEM"];
		$cod_usuario = $_POST ["COD_USUARIO"];
		$ide_na_organizacao = $_POST ["IDE_NA_ORGANIZACAO"];
		$nome_usuario = utf8_encode ( $_POST ["NOME_USUARIO"] );
		$cod_organizacao = $_POST ["COD_ORGANIZACAO"];
		$sigla_organizacao = $_POST ["SIGLA_ORGANIZACAO"];
		$sigla_setor = $_POST ["SIGLA_SETOR"];
		$soe_ticket = $_POST ["SOE_TICKET"];
		$emails = $_POST ["EMAILS"];
		$objetos = $_POST ["OBJETOS"];
		$nomeextobj = utf8_encode ( $_POST ["NOMEEXTOBJ"] );
		$acoes = $_POST ["ACOES"];
		$classes = $_POST ["CLASSES"];
		
		if ($cod_erro) {
			throw new \Exception ( $mensagem );
		}
		$sistema = $this->soe_sigla_sistema;
		
		$url = $this->soe_url_servicos . "?f_metodo=consultaUsuarioCodigo&f_parametros=$cod_usuario&f_codUsuarioSes=$cod_usuario&f_ticket=$soe_ticket";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$json = curl_exec ( $ch );
		curl_close ( $ch );
		
		$obj = json_decode ( utf8_encode ( $json ), true );
		if (isset ( $obj ["s_msgErro"] )) {
			if ($obj ["s_msgErro"] != null) {
				throw new \Exception ( $obj ["s_codErro"] . " - " . $obj ["s_msgErro"] );
			}
		} else {
			echo "<i>Nome:</i> " . $obj ["nomeUsuario"] . "<br />";
			echo "<i>Organização:</i> " . $obj ["siglaOrganizacao"] . "<br />";
			echo "<i>Matrícula:</i> " . $obj ["ideNaOrganizacao"] . "<br />";
			echo "<i>Setor:</i> " . $obj ["siglaSetor"] . "<br />";
			echo "<br/><i>Documentos identidade:</i><br/>";
			foreach ( $obj ["listaDocIdentidade"] as $docIde ) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $docIde ["tipoDocumento"] . " - " . $docIde ["ideDocumento"] . "<br />";
			}
		}
		return $person;
	}
	public function refreshUser(UserInterface $user) {
		if (! $user instanceof WebserviceUser) {
			throw new UnsupportedUserException ( sprintf ( 'Instances of "%s" are not supported.', get_class ( $user ) ) );
		}
		
		return $this->loadUserByUsername ( $user->getUsername () );
	}
	public function supportsClass($class) {
		return $class === get_called_class ();
	}
}