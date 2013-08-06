<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('content'); ?>
<div class="row">
	<div class="twelvecol last select_regiao">
		<h2>Voto enviado!</h2>
	</div>
</div>

<?php if($poll) { ?>
    <?php include VIEWS_PATH . 'Polls/questions.php'; ?>
<?php } else { ?>
    <div class="row home">
        <div class="twelvecol last">
            <?php echo $html->link('<button type="button">Voltar para a Página inicial</button>', array('controller' => 'Application', 'action' => 'index'), array('class' => 'home')); ?>
        </div>
    </div>
<? } ?>
<?php endblock('content'); ?>
