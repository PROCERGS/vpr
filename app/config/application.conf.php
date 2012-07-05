<?php
PDOUtils::setDefaultDatabase('seplag_vpr');

Config::set('votes.pageSize', 50);

Config::set('sms.policy', 'ALLOW');
Config::set('sms.allowedRegionsId', array(19, 22));
Config::set('sms.user', 'seplag');
Config::set('sms.password', 'ckxx55');

/** Contantes de Tempo **/
define('SECOND', 1);
define('MINUTE', 60 * SECOND);
define('HOUR', 60 * MINUTE);
define('DAY', 24 * HOUR);

/** Cache Configuration **/
Config::set('cache.timeout', 3 * MINUTE);
if (Util::getEnvironmentName() == 'local')
	Config::set('cache.path', sys_get_temp_dir() . '/');
else
	Config::set('cache.path', ini_get("upload_tmp_dir").'/');