<?php
PDOUtils::setDefaultDatabase('seplag_vpr');

Config::set('votes.pageSize', 50);

Config::set('sms.policy', SmsVote::ALLOW);
Config::set('sms.allowedRegionsId', array());