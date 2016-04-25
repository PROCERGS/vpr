<?php

namespace PROCERGS\VPR\CoreBundle\Soe;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class SoeUser implements UserInterface, EquatableInterface {
	
	
	public function getRoles() {
	}
	public function getPassword() {
	}
	public function getSalt() {
	}
	public function getUsername() {
	}
	public function eraseCredentials() {
	}
	public function isEqualTo(UserInterface $user) {
		if (! $user instanceof SoeUser) {
			return false;
		}
		
		return true;
	}
	
	public function fromPostArray(&$array)
	{
		if (isset($_POST ["COD_ERRO"])) {
			$this->cod_erro = $_POST ["COD_ERRO"];
		}
		if (isset($_POST ["MENSAGEM"])) {
			$this->mensagem = $_POST ["MENSAGEM"];
		}
		if (isset($_POST ["COD_USUARIO"])) {
			$this->cod_usuario = $_POST ["COD_USUARIO"];
		}
		if (isset($_POST ["IDE_NA_ORGANIZACAO"])) {
			$this->ide_na_organizacao = $_POST ["IDE_NA_ORGANIZACAO"];
		}
		if (isset($_POST ["NOME_USUARIO"])) {
			$this->nome_usuario = utf8_encode ( $_POST ["NOME_USUARIO"] );
		}
		if (isset($_POST ["COD_ORGANIZACAO"])) {
			$this->cod_organizacao = $_POST ["COD_ORGANIZACAO"];
		}
		if (isset($_POST ["SIGLA_ORGANIZACAO"])) {
			$this->sigla_organizacao = $_POST ["SIGLA_ORGANIZACAO"];
		}
		if (isset($_POST ["SIGLA_SETOR"])) {
			$this->sigla_setor = $_POST ["SIGLA_SETOR"];
		}
		if (isset($_POST ["SOE_TICKET"])) {
			$this->soe_ticket = $_POST ["SOE_TICKET"];
		}
		if (isset($_POST ["EMAILS"])) {
			$this->emails = $_POST ["EMAILS"];
		}
		if (isset($_POST ["OBJETOS"])) {
			$this->objetos = $_POST ["OBJETOS"];
		}		
		if (isset($_POST ["NOMEEXTOBJ"])) {
			$this->nomeextobj = utf8_encode ( $_POST ["NOMEEXTOBJ"] );
		}
		if (isset($_POST ["ACOES"])) {
			$this->acoes = $_POST ["ACOES"];
		}
		if (isset($_POST ["CLASSES"])) {
			$this->classes = $_POST ["CLASSES"];
		}
	}
}