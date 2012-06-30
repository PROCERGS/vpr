<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Consulta de Cédulas</h2>
		<p>Estão disponíveis para consulta, as cédulas de votação de cada um dos 28 COREDEs, com suas respectivas propostas de Demandas e Prioridades Estratégicas Regionais.</p>
		
		<form action="<?php echo $html->url(array('controller' => 'Cedulas', 'action' => 'consultar')); ?>" method="get">
			<fieldset>
				<p>Selecione, abaixo, sua região de votação (pelo nome do COREDE) e clique em "Consultar". <label for="nm_municipio">Ou, se preferir, também é possível buscar a região através do nome do município, digitando, no mínimo, as 3 primeiras letras.</label></p>
				<label for="regiao_id">Região: </label>
				<?php echo $select_regiao; ?>
				
				<label for="nm_municipio">Município:</label>
				<input type="text" id="nm_municipio" class="municipio" autocomplete="off" />
			</fieldset>
			
			<button type="submit">Consultar</button>
		</form>
		
		<a class="btLink" href="<?php echo $html->url(array('controller' => 'Application', 'action' => 'index')); ?>">
			<button type="button">Voltar para a Página Inicial da Votação de Prioridades - Orçamento 2013</button>
		</a>
		
	</div>
</div>
<?php endblock(); ?>
