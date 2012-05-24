<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
<div class="row">
	<div class="twelvecol last">
		<h1>Cédula de Votação : Verificação</h1>

<?php 	$exceeded = '';
		if (count($votes) > 5) {
			$exceeded = " e remova propostas excedentes"; ?>
		<div class="error">
			<p>Você precisa escolher, <strong>no máximo</strong>, 5 propostas para votar.</p>
			<p>Você pode utilizar a lista a baixo para revisar suas opções e remover as opções excedentes.</p>
		</div>
<?php 	} ?>
	
		<div class="info">
			<p>Por favor, revise suas opções na lista a baixo<?php echo $exceeded; ?>. Quando estiverem de acordo, clique em Confirmar.</p>
		</div>
	</div>
</div>

<div class="row">
	<div class="twelvecol last">
		<form class="vote" action="<?php echo $html->url(array('controller' => 'Votes', 'action' => 'confirm'));?>" method="post">
			<input type="hidden" name="votes_step" value="review" />
<?php 	if (count($votes) > 0) { ?>
			<ul>
<?php 		foreach ($votes as $vote) {
				$proposal = $proposals[$vote->getProposalId()];
				$id = $proposal->getId();
				$selected = Vote::isVoted($proposal)?'checked="checked"':''; ?>
				<li>
					<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
					<label for="proposal<?php echo $id; ?>"><?php echo $proposal->getLabel(); ?></label>
				</li>
<?php 		} ?>
			</ul>
<?php 	} else { ?>
			<p>Nenhuma proposta selecionada.</p>
<?php 	} ?>
	
			<button type="button" class="back">Voltar</button>
			<button type="submit">Confirmar</button>
		</form>
	</div>
</div>
<?php endblock() ?>
