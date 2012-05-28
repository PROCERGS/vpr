<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main'); ?>
<form class="vote" action="<?php echo $nextURL;?>" method="post">
	<div class="row content_head">
		<div class="sixcol">
			<h2>Cédula de Votação : Passo <?php echo $step; ?></h2>
		</div>
		<div class="sixcol last">
			<button type="button" class="finish">Pular para o fim</button>
		</div>
	</div>
	
	<div class="row">
		<div class="twelvecol last">
			<p>Selecione as propostas que julgar prioritárias. Você está na página <?php echo $step ?> de <?php echo $totalSteps ?></p>
			<input type="hidden" name="votes_step" value="<?php echo $step; ?>" />
			<fieldset>
				<legend>Propostas Disponíveis</legend>
				<ul>
<?php 	foreach ($proposals as $proposal) {
			$id = $proposal->getOptionId();
			$selected = Vote::isVoted($proposal)?'checked="checked"':''; ?>
					<li>
						<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> /><label for="proposal<?php echo $id; ?>"><?php echo $proposal->getLabel(); ?></label>
					</li>
<?php 	} ?>
				</ul>
			</fieldset>
			<button type="button" class="back">Voltar</button>
			<button type="submit">Próximo</button>
		</div>
	</div>
</form>
<?php endblock() ?>
