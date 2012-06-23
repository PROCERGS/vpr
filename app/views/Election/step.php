<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<form class="vote" action="<?php echo $nextURL;?>" method="post">
	<div class="container">
		<div class="row content_head">
			<div class="sixcol">
				<h3>Instruções</h3>
				<p>Selecione até <?php echo $maxSelections?> opções da lista abaixo.</p>
				<p>Clique na proposta para visualizar o seu detalhamento.</p>
			</div>
			<div class="sixcol last">
				<button type="button" class="finish">Pular para o fim</button>
			</div>
		</div>
	
		<div class="row group">
			<div class="twelvecol last">
				<h2><?php echo $group->getNmGrupoDemanda(); ?></h2>
				<input type="hidden" name="votes_step" value="<?php echo $step; ?>" />
				<fieldset>
					<legend>Propostas Disponíveis</legend>
					<dl>
<?php 	foreach ($areas as $options) { ?>
						<dt><?php echo $areasTematicas[$options[0]->getIdAreaTematica()]->getNmAreaTematica(); ?></dt>
<?php 		foreach ($options as $option) { ?>
<?php 			$id = $option->getIdCedula();
				$selected = Vote::isVoted($option)?'checked="checked"':''; ?>
						<dd>
							<input type="checkbox" name="selected[]" value="<?php echo $id; ?>" id="proposal<?php echo $id; ?>" <?php echo $selected; ?> />
							<label for="proposal<?php echo $id; ?>">
								<span class="cod_projeto"><?php echo $option->getCodProjeto(); ?></span> - <?php echo $option->getNmDemanda(); ?></label>
						</dd>
<?php 		}
		} ?>
					</dl>
				</fieldset>
				<button type="button" class="back">Voltar</button>
				<button type="submit">Próximo</button>
			</div>
		</div>
	</div>
</form>
<?php endblock() ?>
