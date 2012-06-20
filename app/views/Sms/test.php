<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo "SMS TEST"; endblock(); ?>
<?php startblock('main'); ?>
<form action="<?php echo $html->url(array('controller' => 'Sms', 'action' => 'receive'))?>" target="console">
	<label for="id">Id:</label>
	<input type="text" name="id" />
	
	<label for="from">From:</label>
	<input type="text" name="from" />
	
	<label for="to">To:</label>
	<input type="text" name="to" />
	
	<label for="msg">Msg:</label>
	<input type="text" name="msg" />
	
	<label for="account">Account:</label>
	<input type="text" name="account" />
	
	<button type="submit">VAI!</button>
</form>

<iframe src="about:blank" frameborder="0" id="console" name="console" width="100%" height="800"></iframe>
<?php endblock('main'); ?>