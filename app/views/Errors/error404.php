<?php http_response_code(404) ?>
<?php require VIEWS_PATH.'main.php'; ?>
<?php startblock('content') ?>
<h1>Page not found</h1>
<?php if (isset($error['message'])) { echo "<p>".$error['message']."</p>"; } ?>
<?php endblock() ?>
