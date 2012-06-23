<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); $html = HTMLHelper::cast($html); ?>
<div class="row">
	<div class="twelvecol last">
		<p>Bem-vindo(a), <?php echo $currentUser->getEleitorTre()->getNmEleitor(); ?></p>
		
		<p><?php echo $currentUser->getRegiao()->getNmRegiao(); ?></p>
		<p>Local de votação: <?php echo $currentUser->getEleitorTre()->getDsLocalVotacao(); ?></p>
		<p>Título de Eleitor: <?php echo $currentUser->getNroTitulo(); ?></p>
		
		<?php echo $html->link('Continuar', array('controller' => 'Election', 'action' => 'step', 'step' => 1)); ?>
	</div>
</div>
<?php endblock(); ?>
