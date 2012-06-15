<?php
// - Application Root
$router->map('/', array('controller' => 'Application'));

// - Application Routes
$router->map('/voto/passo/:step', array('controller' => 'Votes', 'action' => 'step'), array('step' => '[0-9]+'));
$router->map('/votar/passo/:step', array('controller' => 'Election', 'action' => 'step'), array('step' => '[0-9]+'));
$router->map('/voto/revisao', array('controller' => 'Votes', 'action' => 'review'));
