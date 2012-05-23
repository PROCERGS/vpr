<?php if (count(Controller::getJavascriptVars()) > 0) { ?>
		<script>
<?php 	foreach (Controller::getJavascriptVars() as $key => $value) {?>
			var <?php echo $key; ?> = <?php echo $value; ?>;
<?php 	} ?>
		</script>
<?php }
	  foreach (Controller::getJavascripts() as $file) {?>
		<script src="<?php echo $file; ?>"></script>
<?php } ?>