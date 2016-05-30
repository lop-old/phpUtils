<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */



# dump()
if (!\function_exists('dump')) {
	function dump($var) {
		return \pxn\phpUtils\dump(
			$var
		);
	}
}

# d()
if (!\function_exists('d')) {
	function d($var) {
		return \pxn\phpUtils\d(
			$var
		);
	}
}

# dd()
if (!\function_exists('dd')) {
	function dd($var) {
		return \pxn\phpUtils\dd(
			$var
		);
	}
}

# ExitNow()
if (!\function_exists('ExitNow')) {
	function ExitNow($code) {
		return \pxn\phpUtils\ExitNow(
			$code
		);
	}
}

# fail()
if (!\function_exists('fail')) {
	function fail($msg, $code=1) {
		return \pxn\phpUtils\fail(
			$msg,
			$code
		);
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
		return \pxn\phpUtils\debug(
			$debug
		);
	}
}
