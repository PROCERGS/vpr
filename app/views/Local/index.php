<?php require VIEWS_PATH . 'main.php'; ?>
<?php
startblock('title');
echo self::getTitle();
endblock();
?>
<?php startblock('main'); ?>
<div class="row">

	<div class="city_select">
		<?= $city_select ?>
	</div>

	<div class="descricao">
		<?= $string ?>		
	</div>
	<!--	<div class="corede_select">
			< ?= $corede_select ?>
		</div>-->
</div>
<?php endblock() ?>