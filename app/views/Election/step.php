<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<?php $readonly = !isset($nextURL) || (isset($readonly) && $readonly); ?>
<?php if (!$readonly) { ?>
<form class="vote" action="<?php echo $nextURL;?>" method="post">
<?php } ?>
	<div class="container">
<?php foreach ($groups as $currentGroup) {
		$group = $currentGroup['group']; ?>
		<div class="row content_head">
			<div class="sixcol">
				<h3>Instruções</h3>
				<p>Selecione até <?php echo $group->getQtdMaxEscolha(); ?> opções da lista abaixo.</p>
				<p>Clique na proposta para visualizar o seu detalhamento.</p>
			</div>
			<div class="sixcol last">
<?php 		if (!$readonly) { ?>				<button type="button" class="finish">Pular para o fim</button><?php } ?>
			</div>
		</div>
		<div class="row group">
			<div class="twelvecol last">
				<h2><?php echo $group->getNmGrupoDemanda(); ?></h2>
<?php 		if (!$readonly) { ?>				<input type="hidden" name="votes_step" value="<?php echo $step; ?>" /><?php } ?>
				<fieldset>
					<legend>Propostas Disponíveis</legend>
<?php 		if ($group->getFgTituloSimples() == 1)
				include VIEWS_PATH.'Election/titulo_simples.php';
			else
				include VIEWS_PATH.'Election/titulo_agrupado.php'; ?>
				</fieldset>
<?php 		if (!$readonly) { ?>
				<button type="button" class="back">Voltar</button>
				<button type="submit">Próximo</button>
<?php 		} ?>
			</div>
		</div>
<?php } ?>
	</div>
<?php 		if (!$readonly) { ?>
</form>
<?php } ?>
<?php endblock() ?>
