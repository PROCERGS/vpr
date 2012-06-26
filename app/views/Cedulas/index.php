<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('page_name'); ?>Consulta de Cédulas de Votação<?php endblock('page_name'); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<form action="<?php echo $html->url(array('controller' => 'Cedulas', 'action' => 'consultar')); ?>" method="get">
			<fieldset>
				<legend>Por favor, escolha a região desejada e clique em <strong>Consultar</strong></legend>
				<label for="regiao_id">Região: </label> <?php echo $select_regiao; ?>
			</fieldset>
			
			<button type="submit">Consultar</button>
		</form>
	</div>
</div>
<?php endblock(); ?>
