<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('page_name'); ?>Consulta de Locais de Votação<?php endblock('page_name'); ?>
<?php startblock('content'); ?>
<div class="ui-widget">
	<label for="municipio">Digite o município correspondente&nbsp;</label>
	<input class="municipio" />
	<div class="descricao">&nbsp;</div>
</div>
<?php endblock('content'); ?>