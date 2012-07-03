<?php require VIEWS_PATH . 'main.php'; ?>
<?php startblock('title');
echo self::getTitle();
endblock(); ?>
<?php startblock('content'); ?>
<iframe width='910' height='530' frameborder='0' src='/pagina.html' scrolling=no></iframe>
<?php endblock(); ?>