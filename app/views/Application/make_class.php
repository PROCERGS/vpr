<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>ClassMaker</title>
	</head>
	<body>
		<form action="<?php echo $html->url(array('controller' => 'Application', 'action' => 'make_class'), array('table' => $tableName, 'class' => $class_name, 'save' => 'true'))?>" method="get">
			<?php echo $html->select($tables, '-1', array('default_option' => 'Select a table', 'id_name' => 'table', 'label_name' => 'table'), array('name' => 'table')); ?>
			<button type="submit">Generate</button>
		</form>
		<h1>ClassMaker</h1>
		<h2><?php echo $class_name; ?></h2>
		<h3><?php echo $class_filename; ?></h3>
		
		<pre><?php echo htmlentities($class); ?></pre>
		
		<form action="<?php echo $html->url(array('controller' => 'Application', 'action' => 'make_class'), array('table' => $tableName, 'class' => $class_name, 'save' => 'true'))?>" method="post">
			<button type="submit">Save</button>
		</form>
	</body>
</html>
