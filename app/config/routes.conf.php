<?php
// - Application Root
$router->map('/', array('controller' => 'Application'));

// - Application Routes
$router->map('/voto/passo/:step', array('controller' => 'Votes', 'action' => 'step'), array('step' => '[0-9]+'));
$router->map('/voto/revisao', array('controller' => 'Votes', 'action' => 'review'));

// - Mobile Routes
$router->map('/m/', array('controller' => 'Application', 'mobile' => TRUE));
$router->map('/m/:controller/:action', array('mobile' => TRUE));
