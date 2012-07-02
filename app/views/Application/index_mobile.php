<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('page_class') ?>home_mobile<?php endblock() ?>
<?php startblock('content'); ?>
<div class="row online_voting_dates">
	<div class="twelvecol last">
		<p><?php echo $string_data; ?></p>
	</div>
</div>
<div class="row">
	<div class="twelvecol last home">
		<div class="vote_now">
<?php 	if ($show_vote_now)
			echo $html->link("Para votar, clique aqui!", array('controller' => 'Election', 'action' => 'start'));
		else
			echo '<span class="unavailable">Votação não disponível</span>'; ?>
		</div>
	</div>
</div>
<?php endblock(); ?>
