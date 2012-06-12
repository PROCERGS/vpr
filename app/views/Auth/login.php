<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
<form action="<?php echo $html->url(array('controller' => 'Auth', 'action' => 'login')) ?>" method="post">
	<fieldset>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Número do Título de Eleitor</label>
				<input type="number" name="Cidadao[nro_titulo]" id="titulo" min="0" />
			</div>
		</div>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Número do RG</label>
				<input type="number" name="Cidadao[rg]" id="titulo" min="0" />
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="twelvecol last">
			<button type="submit">Continuar</button>
		</div>
	</div>
</form>
<?php var_dump(Cidadao::validateRG_RS(4084575556)); ?>
<?php endblock() ?>
