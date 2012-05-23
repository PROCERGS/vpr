<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
	<h1>Cédula de Votação : Passo <?php echo $step; ?></h1>

	<form class="vote" action="<?php echo $nextURL;?>" method="post">
		<input type="hidden" name="votes_step" value="<?php echo $step; ?>" />
		<ul>
<?php 	foreach ($proposals as $proposal) {
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
