<?php
/**
 * Global configuration file.
 * 
 * ALL FIELDS ARE REQUIRED!
 */

$config = array();

// deployment status: development / production
$config['environment'] = 'development';

// PHP session configuration
$config['session'] = array(
		'name' => 'SIMPLIFY_SESSION', 
		'duration' => 2592000, // 30 days
	);

$config['site_name'] = 'Simplify';

// template system configuration

$config['template'] = array(
		'dir' => SITE_ROOT.'/templates/',
		'cache' => SITE_ROOT.'/tmp/',
	);

// content provider services

$config['services'] = array('facebook', 'googleplus');
