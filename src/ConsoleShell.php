<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class ConsoleShell {
	private final function __construct() {}



	public static function getConsoleVars() {
		if (self::$consoleFlags !== NULL) {
			return self::$consoleFlags;
		}
		global $argv;
		$flags = [];
		for ($index=1; $index<count($argv); $index++) {
			$arg = $argv[$index];
			// --flag
			if (Strings::StartsWith($arg, '--')) {
				// --flag=value
				$pos = \mb_strpos($arg, '=');
				if ($pos !== FALSE) {
					$val = \mb_substr($arg, $pos);
					$arg = \mb_substr($arg, 0, $pos);
					$flags[$arg] = $val;
					$_GET[$arg]  = $val;
					continue;
				}
				// --flag value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						$flags[$arg] = $val;
						$_GET[$arg]  = $val;
						continue;
					}
				}
				// --flag
				if (!isset($flags[$arg])) {
					$flags[$arg] = TRUE;
					$_GET[$arg]  = TRUE;
				}
				continue;
			}
			// -flag
			if (Strings::StartsWith($arg, '-')) {
				// attached value
				$len = \mb_strlen($arg);
				if ($len > 2) {
					$val = \mb_substr($arg, 2);;
					$arg = \mb_substr($arg, 0, 2);
					$flags[$arg] = $val;
					$_GET[$arg]  = $val;
					continue;
				}
				// -f value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						$flags[$arg] = $val;
						$_GET[$arg]  = $val;
						continue;
					}
				}
				// -f
				if (!isset($flags[$arg])) {
					$flags[$arg] = TRUE;
					$_GET[$arg]  = TRUE;
				}
				continue;
			}
			return "Unknown argument: $arg";
		}
		self::$consoleFlags = $flags;
		return $flags;
	}



}
