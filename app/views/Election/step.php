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
					<dl>
<?php 		$areasTematicas = $currentGroup['areasTematicas'];
			if ($group->getFgTituloSimples() == 1) {
				$groupByAreaTematica = FALSE;
				$options = $currentGroup['options'];
				
				include VIEWS_PATH.'Election/option.php';
			} else {
				foreach ($currentGroup['areas'] as $idArea => $options) {
					if ($idArea > 0)
						$nm_area_tematica = $areasTematicas[$idArea]->getNmAreaTematica();
					else
						throw new ErrorException("Inconsistent database: Cedula[".$option->getIdCedula()."] has no id_area_tematica!"); ?>
						<dt><?php echo $nm_area_tematica ?></dt>
<?php 				$groupByAreaTematica = TRUE;
					include VIEWS_PATH.'Election/option.php';
				}
			} ?>
					</dl>
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
