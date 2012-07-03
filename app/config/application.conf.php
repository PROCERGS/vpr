<?php
PDOUtils::setDefaultDatabase('seplag_vpr');

Config::set('votes.pageSize', 50);

Config::set('sms.policy', 'ALLOW');
Config::set('sms.allowedRegionsId', array(19, 22));
Config::set('sms.user', 'seplag');
Config::set('sms.password', 'ckxx55');