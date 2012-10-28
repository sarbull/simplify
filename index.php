<?php
/**
 * Main entry point (router).
 */

require_once(dirname(__FILE__)."/lib/init.php");

// this variable is populated by apache mod_rewrite
$url = $_GET['request_url'];

// dispatch the request to a controller
Controller::dispatch($url);