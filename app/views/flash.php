<?php if (AppController::flash()) {
		$flash = AppController::getFlash(); ?>
<div class="flash <?php echo $flash->getType();?>">
	<p><?php echo $flash->getMessage(); ?></p>
</div>
<?php } ?>