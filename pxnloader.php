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
