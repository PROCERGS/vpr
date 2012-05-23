<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
	<h1>Início da Votação</h1>
	
<?php printr($proposals); ?>
<?php endblock() ?>
