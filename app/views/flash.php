<?php if (AppController::flash()) {
		$flash = AppController::getFlash();
		$flash_extra = $flash->getExtra(); ?>
<div class="flash <?php echo $flash->getType();?>">
	<p><?php echo $flash->getMessage(); ?></p>
</div>
<?php } ?>