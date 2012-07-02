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
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec commodo lacus at metus sollicitudin scelerisque. In et lectus diam. In convallis mollis ante, sit amet placerat sem ullamcorper eget. Curabitur euismod egestas nisl, sit amet tristique lorem euismod ac. Duis fermentum, nisl et varius molestie, ipsum sapien molestie nibh, vel tincidunt sapien nisl ac elit. Integer ac tempus purus.</p>
		<?php echo $html->link('<button type="button">Responder à pesquisa</button>', '#'); ?>
		<?php echo $html->link('<button type="button">Voltar para a Página inicial</button>', array('controller' => 'Application', 'action' => 'index')); ?>
	</div>
</div>
<?php endblock('content'); ?>
