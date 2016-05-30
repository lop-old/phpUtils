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

	protected static $inited = FALSE;
	protected static $config = [
		'is shell'    => NULL,
		'render type' => '',
	];



	public static function init() {
		if (self::$inited)
			return;
		self::$inited = TRUE;
		// detect shell
		self::$config['is shell'] = System::isShell();
	}



	public static function set($key, $value) {
		$key = \strtolower($key);
		self::$config[$key] = $value;
	}
	public static function get($key) {
		$key = \strtolower($key);
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		return NULL;
	}



//TODO: remove this - handled by System.php
//	public static function isShell() {
//		return self::$config['is shell'];
//	}



	public static function getRenderType() {
		$renderType =
			isset(self::$config['render type'])
			? self::$config['render type']
			: '';
		return
			empty($renderType)
			? 'main'
			: $renderType;
	}



}
