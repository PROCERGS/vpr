<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
<div id="conteiner">
	<div id="cabecalho">
		<div id="logotipo">
			<h1>Sistema Estadual de Participação Popular e Cidadã</h1>
			<img src="../images/logotipo_internas.png" alt="Votação de Prioridades - Orçamento 2013" width="265" height="104" border="0" />
		</div>
		<div id="brasaoEstadoBG">
            <a href="http://www.estado.rs.gov.br/" target="_blank"><img src="../images/logoGoverno.png" alt="Logotipo do Governo" title="Governo do Estado"  border="0"/></a>
		</div>
	</div>
	<div id="identificacao">    
		<h2>Locais de votação - Orçamento 2013</h2>
		<b>Procure pelo município os locais de votação</b><br />...</div>
	<div class="main"> 
		<div class="ui-widget">
			<label for="municipio">Digite o município correspondente</label>
			<input class="municipio" />
			<div class="descricao">
				&nbsp;
			</div>
		</div>
	</div>
	<div id="rodape">
		<ul>
			<li><a href="<?= $html->url(array('controller' => 'Locais', 'action' => 'comoVotar')) ?>">Como Votar</a></li>
			<li><a href="http://www.participa.rs.gov.br/" target="_blank">Portal da Participação</a></li>
			<li><a href="http://www.procergs.rs.gov.br/" target="_blank">PROCERGS</a></li>
			<li style="border: none"><a href="http://www.estado.rs.gov.br/" target="_blank">Site Governo do Estado do RS</a></li>     
		</ul>
	</div>
</div>
<?php endblock('main'); ?>