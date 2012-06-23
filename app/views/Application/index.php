<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
		<div class="container home">
			<div class="static_bg">
				<div class="row header">
					<div class="sixcol">
						<h1>Sistema Estadual de Participação Popular e Cidadã</h1>
					</div>
					<div class="sixcol last">
						<h2><?= $string_alt_img ?></h2>
					</div>
				</div>
				<div class="row online_voting_dates">
					<div class="twelvecol last">
						<p><?= $string_data ?></p>
					</div>
				</div>
				<?= $button ?>
				<div class="row queries">
					<div class="sixcol">
						<?php echo $html->link("Consulte a cédula de sua região", array('controller' => 'Cedulas', 'action' => '')); ?>
					</div>
					<div class="sixcol last">
						<?php echo $html->link("Consulte os locais de votação", array('controller' => 'Locais', 'action' => '')); ?>
					</div>
				</div>
				<div class="row footer menu">
					<ul class="twelvecol last">
						<li><?php echo $html->link("Como Votar", array('controller' => 'Cedulas', 'action' => '')); ?></li>
						<li><?php echo $html->link("Portal da Participação", "http://www.participa.rs.gov.br/"); ?></li>
						<li><?php echo $html->link("Site do Governo do Estado do RS", "http://www.estado.rs.gov.br/"); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="twelvecol footer last">
					<a class="procergs" href="http://www.procergs.rs.gov.br/" target="_blank"><img src="/images/procergs_branco.png" border="0" /></a>
				</div>
			</div>
		</div>
<?php endblock(); ?>
