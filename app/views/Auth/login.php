<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
<form action="<?php echo $html->url(array('controller' => 'Auth', 'action' => 'login')) ?>" method="post">
	<fieldset>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Número do Título de Eleitor</label>
				<input type="number" name="Voter[voter_registration]" id="titulo" min="0" />
			</div>
		</div>
		<div class="row">
			<div class="twelvecol last">
				<label for="titulo">Data de Nascimento</label>
				<input type="date" name="Voter[birth_date]" id="titulo" min="0" />
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
