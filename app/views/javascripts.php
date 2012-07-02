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
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-33078292-1']);
  _gaq.push(['_setDomainName', 'rs.gov.br']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
