<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
<div id="conteiner">
	<div id="identificacao"> 
		<div class="ui-widget">
			<label for="municipio">Digite o município correspondente</label>
			<input class="municipio" />
			<div class="descricao">
			</div>
		</div>
	</div>
</div><!-- End demo -->
<?php endblock() ?>