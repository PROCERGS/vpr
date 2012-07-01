<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last home">
		<div class="titulo_pagina">Votação de Prioridades<br />Orçamento 2013</div>
		<div class="vote_now">
<?php 	if ($show_vote_now)
			echo $html->link("Para votar, clique aqui!", array('controller' => 'Election', 'action' => 'start'));
		else
			echo '<span class="unavailable">Votação não disponível</span>'; ?>
		</div>
	</div>
</div>
<?php endblock(); ?>
