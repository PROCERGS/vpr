<?php http_response_code(500) ?>
<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title'); echo self::getTitle(); endblock(); ?>
<?php startblock('main') ?>
<h1>Ocorreu um Erro Interno</h1>
<p><?php echo $error['message'] ?></p>
<?php endblock() ?>