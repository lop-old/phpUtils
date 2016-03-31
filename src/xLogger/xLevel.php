<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger;

use pxn\phpUtils\Defines;
use pxn\phpUtils\Numbers;


final class xLevel {
	private function __construct() {}


	const XLEVEL_OFF     = Defines::INT_MAX;
	const XLEVEL_STDERR  = 9000;
	const XLEVEL_STDOUT  = 8000;
	const XLEVEL_FATAL   = 2000;
	const XLEVEL_SEVERE  = 1000;
	const XLEVEL_WARNING = 900;
	const XLEVEL_INFO    = 800;
	const XLEVEL_STATS   = 700;
	const XLEVEL_FINE    = 500;
	const XLEVEL_FINER   = 400;
	const XLEVEL_FINEST  = 300;
	const XLEVEL_ALL     = Defines::INT_MIN;

	protected static $knownLevels = [
		'OFF'     => self::XLEVEL_OFF,
		'ERR'     => self::XLEVEL_STDERR,
		'OUT'     => self::XLEVEL_STDOUT,
		'FATAL'   => self::XLEVEL_FATAL,
		'SEVERE'  => self::XLEVEL_SEVERE,
		'WARNING' => self::XLEVEL_WARNING,
		'INFO'    => self::XLEVEL_INFO,
		'STATS'   => self::XLEVEL_STATS,
		'FINE'    => self::XLEVEL_FINE,
		'FINER'   => self::XLEVEL_FINER,
		'FINEST'  => self::XLEVEL_FINEST,
		'ALL'     => self::XLEVEL_ALL,
	];



	public static function FindLevel($value) {
		if (empty($value))
			return NULL;
		// number value
		if (Numbers::isNumber($value)) {
			$value = (int) $value;
			if ($value == self::XLEVEL_OFF)
				return self::XLEVEL_OFF;
			if ($value == self::XLEVEL_ALL)
				return self::XLEVEL_ALL;
			// find nearest value
			$level  = self::XLEVEL_OFF;
			$offset = self::XLEVEL_OFF;
			foreach (self::$knownLevels as $key => $val) {
				if ($val == self::XLEVEL_OFF)
					continue;
				if ($value < $val)
					continue;
				if ($value - $val < $offset) {
					$offset = $value - $val;
					$level = $val;
				}
			}
			return $level;
		}
		// word value
		$value = \strtoupper($value);
		if (isset(self::$knownLevels[$value])) {
			return self::$knownLevels[$value];
		}
		// unknown level
		return NULL;
	}
	public static function FindLevelName($value) {
		$level = self::FindLevel($value);
		if ($level == NULL)
			return NULL;
		return self::LevelToName($level);
	}
	public static function LevelToName($value) {
		if (empty($value))
			return NULL;
		foreach (self::$knownLevels as $key => $val) {
			if ($val == $value)
				return $key;
		}
		return NULL;
	}



}
