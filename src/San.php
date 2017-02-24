<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class San {
	private final function __construct() {}



	public static function AlphaNum($str) {
		return \preg_replace("/[^a-z0-9]+/i", '', $str);
	}
	public static function AlphaNumSafe($str) {
		return \preg_replace("/[^a-z0-9-._]+/i", '', $str);
	}
	public static function AlphaNumSafeMore($str) {
		return \preg_replace("/[^a-z0-9-._:]+/i", '', $str);
	}
	public static function AlphaNumSpaces($str) {
		return \preg_replace("/[^\sa-z0-9-_]+/i", '', $str);
	}
	public static function AlphaNumUnderscore($str) {
		return \preg_replace("/[^a-z0-9_]+/i", '', $str);
	}
	public static function AlphaNumFile($str) {
		$filter = '[:alnum:]\(\)\_\.\,\'\&\?\+\-\=\!';
		return \preg_replace('/[^'.$filter.']/', '', $str);
	}



	public static function isAlphaNum($str) {
		$str = (string) $str;
		$compare = self::AlphaNum($str);
		return $compare === $str;
	}
	public static function isAlphaNumSafe($str) {
		$str = (string) $str;
		$compare = self::AlphaNumSafe($str);
		return $compare === $str;
	}
	public static function isAlphaNumSafeMore($str) {
		$str = (string) $str;
		$compare = self::AlphaNumSafeMore($str);
		return $compare === $str;
	}
	public static function isAlphaNumSpaces($str) {
		$str = (string) $str;
		$compare = self::AlphaNumSpaces($str);
		return $compare === $str;
	}
	public static function isAlphaNumUnderscore($str) {
		$str = (string) $str;
		$compare = self::AlphaNumUnderscore($str);
		return $compare === $str;
	}
	public static function isAlphaNumFile($str) {
		$str = (string) $str;
		$compare = self::AlphaNumFile($str);
		return $compare === $str;
	}



	public static function SafePath($path) {
		$path = Strings::Trim($path, ' ');
		if (empty($path))
			$path = \getcwd();
		$temp = \realpath($path);
		if (empty($temp)) throw new \Exception(\sprintf('Path not found! %s', $path));
		$path = $temp;
		unset($temp);
		return $path.'/';
	}
	public static function SafeDir($dir) {
		$dir = Strings::Trim($dir, ' ', '/');
		if (empty($dir)) throw new \Exception('dir argument is required');
		$temp = self::AlphaNumSafe($dir);
		if ($dir != $temp) {
			throw new \Exception(sprintf(
				'dir argument contains illegal characters! %s != %s',
				$dir,
				$temp
			));
		}
		unset($temp);
		if (Strings::StartsWith($dir, '.'))    throw new \Exception('Invalid dir argument, cannot start with .');
		if (\mb_strpos($dir, '..') !== FALSE)  throw new \Exception('Invalid dir argument, cannot contain ..');
		return $dir.'/';
	}



}
