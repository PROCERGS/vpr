<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php startblock('title') ?>YOUR DEFAULT TITLE<?php endblock() ?></title>
<?php startblock('css'); require_once 'stylesheets.php'; endblock('css'); ?>
	</head>
	<body>
	<div class="container">
		<div class="row">
			<div class="threecol"></div>
			<div class="fivecol">
				<h1><?php startblock('header_title') ?>Votação de Prioridades do Orçamento 2013<?php endblock() ?></h1>
			</div>
		</div>
<?php emptyblock('main') ?>
	</div>
<?php startblock('javascript'); require_once 'javascripts.php'; endblock('javascript'); ?>
	</body>
</html>
