<?php
if (defined('SITE_ROOT')) return;
define('SITE_ROOT', dirname(dirname(__FILE__)));

require_once(SITE_ROOT.'/config/global.php');
require_once(SITE_ROOT.'/config/routes.php');
require_once(SITE_ROOT.'/config/database.php');

if (!isset($config['environment']) || ($config['environment'] != 'production')) {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', 'On');
}

// autodetect web root
if (!defined('WEBROOT')) {
	$webroot = 'http'.(empty($_SERVER['HTTPS'])||($_SERVER['HTTPS']=='off')?'':'s').'://'.
		(empty($_SERVER['HTTP_HOST']) ? 
			$_SERVER['SERVER_ADDR'] : 
			$_SERVER['HTTP_HOST']).dirname($_SERVER['PHP_SELF']);
	if (!preg_match('/\/$/i', $webroot)) $webroot.='/';
	define('WEBROOT', $webroot);
}

// load libraries
require_once(SITE_ROOT.'/lib/class.pstt.php');
require_once(SITE_ROOT.'/lib/class.simpledb.php');
require_once(SITE_ROOT.'/lib/class.simplegd.php');
require_once(SITE_ROOT.'/lib/functions.php');
require_once(SITE_ROOT.'/lib/controller.php');

// load simplify libraries
require_once(SITE_ROOT.'/lib/simplify/FeedObject.php');
require_once(SITE_ROOT.'/lib/simplify/BaseService.php');
require_once(SITE_ROOT.'/lib/simplify/ServiceUpdater.php');


function ob_callback($buf) {
	global $config;
	// replace all relative URLs with absolute values
	return preg_replace('/(href="|src="|action=")(?!#|[a-zA-Z]+:|")(?:-|([^"]+))(")/i','$1' . 
		WEBROOT . '$2$3',$buf);
}
if (!defined('NO_OUTPUT_BUFFERING')) ob_start('ob_callback');

// initialize PHP session
ini_set('session.use_only_cookies',true);
ini_set('url_rewriter.tags',"");
ini_set('session.gc_maxlifetime', $config['session']['duration']);
ini_set('session.cache_expire', 43200);
session_save_path(SITE_ROOT . '/tmp');
session_set_cookie_params($config['session']['duration'],'/');
session_name($config['session']['name']);
if (isset($_GET[$config['session']['name']])) {session_id($_GET[$config['session']['name']]);}
session_start();

if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value) {
		$value=is_array($value)?array_map('stripslashes_deep',$value):stripslashes($value);
		return $value;
	}
	$_POST=array_map('stripslashes_deep',$_POST);
	$_GET=array_map('stripslashes_deep',$_GET);
	$_COOKIE=array_map('stripslashes_deep',$_COOKIE);
}

// if the server is ipv6 enabled
if (strpos($_SERVER['REMOTE_ADDR'], '::') === 0) {
	$_SERVER['REMOTE_ADDR']=substr($_SERVER['REMOTE_ADDR'],strrpos($_SERVER['REMOTE_ADDR'], ':')+1);
}

// initialize the database
$db = new simpleDB_mysql($config['database']['host'], $config['database']['user'], 
	$config['database']['password'], $config['database']['database'], false);

// set names to UTF-8
$db->query("SET NAMES utf8");
