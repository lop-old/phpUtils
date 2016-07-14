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

	private static $flags = NULL;
	private static $args  = NULL;



	public static function init() {
		self::initConsoleVars();
	}
	private static function initConsoleVars() {
		if (!System::isShell()) {
			return FALSE;
		}
		if (self::$flags !== NULL || self::$args !== NULL) {
			return FALSE;
		}
		global $argv;
		self::$flags = [];
		self::$args  = [];
		$allArgs = FALSE;
		for ($index=1; $index<count($argv); $index++) {
			$arg = $argv[$index];
			if (empty($arg)) continue;
			// --
			if ($allArgs) {
				self::$args[] = $arg;
				continue;
			}
			if ($arg == '--') {
				$allArgs = TRUE;
				continue;
			}
			// --flag
			if (Strings::StartsWith($arg, '--')) {
				// --flag=value
				$pos = \mb_strpos($arg, '=');
				if ($pos !== FALSE) {
					$val = \mb_substr($arg, $pos);
					$arg = \mb_substr($arg, 0, $pos);
					self::$flags[$arg] = $val;
					//$_GET[$arg] = $val;
					continue;
				}
				// --flag value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						self::$flags[$arg] = $val;
						//$_GET[$arg] = $val;
						continue;
					}
				}
				// --flag
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
					//$_GET[$arg] = TRUE;
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
					self::$flags[$arg] = $val;
					//$_GET[$arg] = $val;
					continue;
				}
				// -f value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						self::$flags[$arg] = $val;
						//$_GET[$arg] = $val;
						continue;
					}
				}
				// -f
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
					//$_GET[$arg] = TRUE;
				}
				continue;
			}
			// not flag, must be argument
			self::$args[] = $arg;
		}
		return TRUE;
	}



	public static function getFlags() {
		return self::$flags;
	}
	public static function getArgs() {
		return self::$args;
	}



	public static function hasFlag($key) {
		return isset(self::$flags[$key]);
	}
	public static function isHelp() {
		return self::hasFlag('-h') ||
			self::hasFlag('--help');
	}



}
