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
<?php 		foreach ($currentGroup['areas'] as $options) {
				$areasTematicas = $currentGroup['areasTematicas']; ?>
						<dt><?php echo $areasTematicas[$options[0]->getIdAreaTematica()]->getNmAreaTematica(); ?></dt>
<?php 			foreach ($options as $option) { ?>
<?php 				$id = $option->getIdCedula();
					$selected = Vote::isVoted($option)?'checked="checked"':''; ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $option->getNmDemanda(); ?></label>
						</dd>
<?php 			}
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
