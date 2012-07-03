<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Voto enviado!</h2>
		<p><strong>Obrigado por sua participação!</strong></p>
	</div>
</div>
<div class="row poll">
	<div class="twelvecol last">
		<h2>Pesquisa Opcional</h2>
		<p>Ajude a melhorar o processo Votação de Prioridades - Orcamento 2013 dedicando alguns minutos para responder a pesquisa abaixo. Todas as respostas são tratadas estatisticamente e, em hipótese alguma, o respondente é identificado.</p>
		<?php echo $html->link('Responder à pesquisa', '#', array('class' => 'pesquisa')); ?>
	</div>
</div>
<div class="row home">
	<div class="twelvecol last">
		<?php echo $html->link('<button type="button">Voltar para a Página inicial</button>', array('controller' => 'Application', 'action' => 'index'), array('class' => 'home')); ?>
	</div>
</div>
<?php endblock('content'); ?>
