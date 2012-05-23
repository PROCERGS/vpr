<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
	<h1>Cédula de Votação : Verificação</h1>
	
	<form class="vote" action="<?php echo $html->url(array('controller' => 'Votes', 'action' => 'confirm'));?>" method="post">
		<ul>
<?php 	foreach ($votes as $vote) {
			$proposal = $proposals[$vote->getProposalId()];
			$id = $proposal->getId();
			$selected = Vote::isVoted($proposal)?'checked="checked"':''; ?>
			<li>
				<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> /><label for="proposal<?php echo $id; ?>"><?php echo $proposal->getLabel(); ?></label>
			</li>
<?php 	} ?>
		</ul>
	
		<button type="button" class="back">Voltar</button>
		<button type="submit">Próximo</button>
	</form>
<?php endblock() ?>
