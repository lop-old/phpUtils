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
	$pwd = getcwd();
	$search_paths = [
			$pwd,
			$pwd.'/..',
			$pwd.'/../..'
	];
	$found = FALSE;
	foreach ($search_paths as $path) {
		if (empty($path)) continue;
		$filepath = \realpath("{$path}/vendor/autoload.php");
		if (empty($filepath)) continue;
		if (!\is_file("{$path}/autoload.php")) {
			$found = TRUE;
			require($filepath);
			break;
		}
	}
	if (!$found) {
		echo "\nFailed to find composer autoload.php !\n".
			"Use 'composer install' to download the required dependencies,\n".
			"or see https://getcomposer.org/download/ for instructions on installing Composer.\n\n";
		exit(1);
	}
}
