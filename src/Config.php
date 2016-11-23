<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class Config {
	private function __construct() {}

	const DEFAULT_RENDER_TYPE = 'main';

	protected static $inited = FALSE;
	protected static $config = [];
	protected static $defaults = [];



	public static function init() {
		if (self::$inited)
			return;
		self::$inited = TRUE;
		// detect shell
		self::$config['is shell'] = System::isShell();
	}



	public static function set($key, $value) {
		$key = \mb_strtolower($key);
		self::$config[$key] = $value;
	}
	public static function setDefault($key, $value) {
		$key = \mb_strtolower($key);
		self::$defaults[$key] = $value;
	}
	public static function get($key) {
		$key = \mb_strtolower($key);
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		if (isset(self::$defaults[$key])) {
			return self::$defaults[$key];
		}
		return NULL;
	}
	public static function peak($key) {
		$key = \mb_strtolower($key);
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		return NULL;
	}



}
