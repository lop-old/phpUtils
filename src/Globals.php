<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */

use pxn\phpUtils\Defines;



# dump()
if (!\function_exists('dump')) {
	function dump($var, $msg=NULL) {
		return \pxn\phpUtils\dump($var, $msg);
	}
}

# d()
if (!\function_exists('d')) {
	function d($var, $msg=NULL) {
		return \pxn\phpUtils\d($var, $msg);
	}
}

# dd()
if (!\function_exists('dd')) {
	function dd($var, $msg=NULL) {
		return \pxn\phpUtils\dd($var, $msg);
	}
}

# ExitNow()
if (!\function_exists('ExitNow')) {
	function ExitNow($code=1) {
		return \pxn\phpUtils\ExitNow($code);
	}
}

# fail()
if (!\function_exists('fail')) {
	function fail($msg=NULL, $exitcode=1, \Exception $e=NULL) {
		return \pxn\phpUtils\fail($msg, $exitcode, $e);
	}
}

# backtrace()
if (!\function_exists('backtrace')) {
	function backtrace() {
		return \pxn\phpUtils\backtrace();
	}
}

# debug()
if (!\function_exists('debug')) {
	function debug($debug=NULL) {
		return \pxn\phpUtils\debug($debug);
	}
}

# pxdb
if (\class_exists('pxn\\pxdb\\dbPool')) {
	if (!\function_exists('pxdb_configure')) {
		function pxdb_configure(
			$dbName,
			$driver,
			$host,
			$port,
			$u,
			$p,
			$database,
			$prefix
		) {
			return \pxn\pxdb\dbPool::configure(
				$dbName,
				$driver,
				$host,
				$port,
				$u,
				$p,
				$database,
				$prefix
			);
		}
	}
}

if (!\function_exists('register_app')) {
	function register_app($classPath) {
		if (!\class_exists($classPath)) {
			fail("App class doesn't exist: $classPath",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		return $classPath::register();
	}
}
