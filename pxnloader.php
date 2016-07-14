<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */



// find autoloader.php
{
	// find entry path
	$entry = '';
	if (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT'])) {
		$entry = $_SERVER['DOCUMENT_ROOT'];
	}
	// find entry path from backtrace (shell mode)
	if (empty($entry)) {
		$trace = \debug_backtrace();
		$last  = \end($trace);
		$entry = \dirname($last['file']);
		unset($trace, $last);
	}
	// look in paths
	$search_paths = [
		$entry,
		$entry.'/..',
		$entry.'/../..',
	];
	// find autoload.php in paths
	$loader = NULL;
	foreach ($search_paths as $path) {
		if (empty($path)) continue;
		$filepath = \realpath("{$path}/vendor/autoload.php");
		if (!empty($filepath) && \is_file($filepath)) {
			$loader = require($filepath);
			if ($loader != NULL) {
				break;
			}
		}
	}
	if ($loader == NULL) {
		echo "\nFailed to find composer autoload.php !\n".
			"Use 'composer install' to download the required dependencies,\n".
			"or see https://getcomposer.org/download/ ".
			"for instructions on installing Composer.\n\n";
		exit(1);
	}
}
