<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
<div class="row">
	<div class="twelvecol last">
		<?php echo $html->link('Votar!', array('controller' => 'Votes', 'action' => 'start')); ?>
	</div>
</div>
<?php endblock() ?>
