<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Consulta de Cédulas</h2>
		<p>Estão disponíveis para consulta, as cédulas de votação de cada um dos 28 COREDEs, com suas respectivas propostas de Demandas e Prioridades Estratégicas Regionais.</p>

		<p>Selecione, abaixo, sua região de votação (pelo nome do COREDE) e clique em "Consultar".</p>
		<form action="<?php echo $html->url(array('controller' => 'Cedulas', 'action' => 'consultar')); ?>" method="get">
			<p>
				<label for="regiao_id">Região: </label> <?php echo $select_regiao; ?>
				<button type="submit">Consultar</button></br></br>
				<label for="municipio"> ou busque a região pelo munícipio, digitando a seguir: &nbsp; </label>
				<input class="municipio" /><div class="descricao" style="visibility: hidden"></div>
			</p>
		</form>
		<br>
		<hr><br>
		<center><a href="<?= $html->url(array('controller' => 'Application', 'action' => 'index')) ?>"><input type="button" value="Voltar para a Página Inicial da Votação de Prioridades - Orçamento 2013" name="Envia"></a></center>
		
	</div>
</div>
<?php endblock(); ?>
