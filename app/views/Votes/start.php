<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); $html = HTMLHelper::cast($html); ?>
<div class="row">
	<div class="twelvecol last">
		<p>Bem-vindo(a), <?php echo $currentUser->getName(); ?></p>
		
		<p><?php echo $currentUser->getRegion(); ?></p>
		<p>Título de Eleitor: <?php echo $currentUser->getVoterRegistration(); ?></p>
		
		<?php echo $html->link('Continuar', array('controller' => 'Votes', 'action' => 'step', 'step' => 1)); ?>
	</div>
</div>
<?php endblock(); ?>
