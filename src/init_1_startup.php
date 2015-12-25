<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace {


// default error reporting
\error_reporting(\E_ALL);
\ini_set('display_errors', 'On');
\ini_set('html_errors',    'On');
\ini_set('log_errors',     'On');
\ini_set('error_log',      'error_log');


// php version 5.6 required
if(\PHP_VERSION_ID < 50600) {
	echo '<p>PHP 5.6 or newer is required!</p>'; exit(1);
}


// atomic defines
if(\defined('pxn\\phpUtils\\INDEX_DEFINE')) {
	echo '<h2>Unknown state! init.php already loaded?</h2>';
	exit(1);
}
if(\defined('pxn\\phpUtils\\PORTAL_LOADED')) {
	echo '<h2>Unknown state! Portal already loaded?</h2>';
	exit(1);
}
\define('pxn\\phpUtils\\INDEX_DEFINE', TRUE);


// timezone
//TODO: will make a config entry for this
try {
	$zone = @date_default_timezone_get();
	if($zone == 'UTC') {
		@date_default_timezone_set(
			'America/New_York'
		);
	} else {
		@date_default_timezone_set(
			@date_default_timezone_get()
		);
	}
	unset($zone);
} catch(\Exception $ignore) {}


}
