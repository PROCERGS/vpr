<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
<?php if ($detector->isiPhone()) { ?>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
<?php } ?>
		<title><?php startblock('title') ?>YOUR DEFAULT TITLE<?php endblock() ?></title>
<?php startblock('css'); require_once 'stylesheets.php'; endblock('css'); ?>
	</head>
	<body>
	<div class="container">
		<div class="row header">
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
