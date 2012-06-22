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
						<h2>Votação de Prioridades</h2>
					</div>
				</div>
				<div class="row online_voting_dates">
					<div class="twelvecol last">
						<p>Período de Votação Online: dias 4 e 5 de julho de 2012</p>
					</div>
				</div>
				<div class="row vote_now">
					<div class="twelvecol last">
						<?php echo $html->link("Para votar, clique aqui!", array('controller' => 'Election', 'action' => 'start')); ?>
					</div>
				</div>
				<div class="row queries">
					<div class="sixcol">
						<?php echo $html->link("Consulte a cédula de sua região", array('controller' => 'Application', 'action' => 'index')); ?>
					</div>
					<div class="sixcol last">
						<?php echo $html->link("Consulte os locais de votação", array('controller' => 'Locais', 'action' => 'index')); ?>
					</div>
				</div>
				<div class="row menu">
					<ul class="twelvecol last">
						<li><?php echo $html->link("Como Votar", array('controller' => 'Locais', 'action' => 'index')); ?></li>
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
