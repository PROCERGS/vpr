<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
<div id="conteiner">
	<div id="identificacao"> 
		<a href="#">
			<div id="logotipo">
				<img src="../images/logotipo.png" alt="Votação de Prioridades - Orçamento 2013" border="0" />
			</div>
		</a>
		<div id="versao">Período de Votação Online: dia 4 de julho de 2012
			<!--hr noshade=”noshade” !--> </div>
		<div id="votar">
			<a href="<?= $html->url(array('controller' => 'Election', 'action' => 'start')); ?>">
				<img src="../images/icone_votar.png" alt="Ícone para Votar" border="0" align="left" />
				Para votar,<br />clique aqui!
			</a>
		</div>
		<div id="cedula"><a href="<?= $html->url(array('controller' => 'Cedulas', 'action' => 'consulte')); ?>"><img src="../images/icone_cedula.png" alt="Ícone para Consulta de Cédula" border="0" align="left" />Consulte a cédula de sua região</a></div>
		<div id="locais">
			<a href="<?= $html->url(array('controller' => 'Locais', 'action' => 'index')); ?>">
				<img src="../images/icone_locais.png" alt="Ícone para Locais de Votação" border="0" align="left" />
				Consulte os locais de votação
			</a>
		</div>
		<div id="rodape"><a href="<?= $html->url(array('controller' => 'Cedulas', 'action' => 'comoVotar')); ?>">Como Votar</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.participa.rs.gov.br/" target="_blank">Portal da Participação</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.estado.rs.gov.br/" target="_blank">Site Governo do Estado do RS</a></div>
	</div>
	<div id="procergs"> <a href="http://www.procergs.rs.gov.br/"><img src="../images/procergs_branco.png" border="0" /></a>
	</div>
</div>
<?php endblock() ?>

