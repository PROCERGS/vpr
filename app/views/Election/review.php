<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<form class="vote review" action="<?php echo $html->url(array('controller' => 'Election', 'action' => 'confirm'));?>" method="post">
	<div class="container">
		<div class="row content_head">
			<div class="twelvecol last">
<?php 	$exceeded = '';
		if (count($votes) > $group->getQtdMaxEscolha()) {
			$exceeded = " e remova propostas excedentes"; ?>
				<div class="error">
					<p>Você precisa escolher, <strong>no máximo</strong>, <?php echo $group->getQtdMaxEscolha(); ?> propostas para votar.</p>
					<p>Você pode utilizar a lista a baixo para revisar suas opções e remover as opções excedentes.</p>
				</div>
<?php 	} ?>
				<div class="info">
					<p>Por favor, revise suas opções na lista a baixo<?php echo $exceeded; ?>. Quando estiverem de acordo, clique em Confirmar.</p>
				</div>
			</div>
		</div>
	</div>
<?php include_once VIEWS_PATH.'flash.php'; ?>
	<div class="row group">
		<div class="twelvecol last">
			<h2><?php echo $group->getNmGrupoAbrev(); ?></h2>
			<input type="hidden" name="votes_step" value="review" />
			<fieldset>
				<legend>Propostas Disponíveis</legend>
				<dl>
<?php 		$options = $selectedOptions;
			$groupByAreaTematica = FALSE;
			include VIEWS_PATH.'Election/option.php'; ?>
				</dl>
			</fieldset>
			
			<button type="button" class="back">Voltar</button>
<?php		if (Config::get('isMobile'))
				$nextText = 'Confirmar';
			else
				$nextText = 'Confirmar'.($next_group?' e continuar votação':''); ?>
			<button type="submit"><?php echo $nextText; ?></button>
		</div>
	</div>
</form>
<?php endblock(); ?>
