<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Voto enviado!</h2>
		<p>Seu voto foi computado com sucesso.</p>
		<p><strong>Obrigado por sua participação!</strong></p>
	</div>
</div>
<div class="row poll">
	<div class="twelvecol last">
		<h2>Pesquisa Opcional</h2>
		<p>Ajude a melhorar o processo de votação, dedicando alguns poucos minutos para responder um pequeno questionário. Suas respostas ficarão totalmente anônimas e serão utilizadas apenas para avaliar o processo de votação de prioridades no Estado do Rio Grande do Sul, Orcamento 2013.</p>
		<?php echo $html->link('Responder à pesquisa', '#', array('class' => 'pesquisa')); ?>
		<?php echo $html->link('<button type="button">Voltar para a Página inicial</button>', array('controller' => 'Application', 'action' => 'index'), array('class' => 'home')); ?>
	</div>
</div>
<?php endblock('content'); ?>
