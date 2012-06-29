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

		<p>Selecione sua região de votação (pelo nome do COREDE) e clique em "Consultar".</p>
		<form action="<?php echo $html->url(array('controller' => 'Cedulas', 'action' => 'consultar')); ?>" method="get">
			<fieldset>
				<label for="regiao_id">Região: </label> <?php echo $select_regiao; ?>
				<label for="nm_municipio"> ou busque através do nome do munícipio:</label>
				<input type="text" id="nm_municipio" class="municipio" autocomplete="off" />
			</fieldset>
			
			<button type="submit">Consultar</button>
		</form>
		<br>
		<hr><br>
		<a href="<?php echo $html->url(array('controller' => 'Application', 'action' => 'index')); ?>">
			<input type="button" value="Voltar para a Página Inicial da Votação de Prioridades - Orçamento 2013" name="Envia">
		</a>
		
	</div>
</div>
<?php endblock(); ?>
