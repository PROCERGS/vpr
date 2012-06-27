<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('content'); ?>
<style>
	h2 {font-size: 14px; margin: 20px 0 20px 0;}
	p{margin: 10px 0 10px 0}
	form{margin: 20px 0 20px 0;}
	a{text-decoration:none; }
</style>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Consulta de Cédulas</h2>
		<p>Estão disponíveis para consulta, as cédulas de votação de cada um dos 28 COREDEs, com suas respectivas propostas de Demandas e Prioridades Estratégicas Regionais.</p>

		<p>Selecione, abaixo, sua região de votação (pelo nome do COREDE) e clique em "Consultar".</p>
		<form action="<?php echo $html->url(array('controller' => 'Cedulas', 'action' => 'consultar')); ?>" method="get">
			<p>
				<label for="regiao_id">Região: </label> <?php echo $select_regiao; ?>
				<button type="submit">Consultar</button>
			</p>
		</form>
		<br>
		<hr><br>
		<center><a href="<?= $html->url(array('controller' => 'Application', 'action' => 'index')) ?>"><input type="button" value="Voltar para a Página Inicial da Votação de Prioridades - Orçamento 2013" name="Envia"></a></center>
<!--		<input class="municipio" /><div class="descricao" style="visibility: hidden"></div>-->
	</div>
</div>
<?php endblock(); ?>
