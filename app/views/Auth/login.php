<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<form action="<?php echo $html->url(array('controller' => 'Auth', 'action' => 'login')) ?>" method="post">
	<fieldset>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Número do Título de Eleitor</label>
				<input type="number" name="Cidadao[nro_titulo]" id="titulo" min="0" value="101926780426" />
			</div>
		</div>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Número do RG</label>
				<input type="number" name="Cidadao[rg]" id="titulo" min="0" value="4084575556" />
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="twelvecol last">
			<button type="submit">Continuar</button>
		</div>
	</div>
</form>
<?php endblock() ?>
