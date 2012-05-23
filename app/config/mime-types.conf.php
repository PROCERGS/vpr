<?php
$custom_mimetypes = array(
	
);
 
$custom_mimetypes = array_merge(Config::get('mimetypes'), $custom_mimetypes);
Config::set('mimetypes', $custom_mimetypes);