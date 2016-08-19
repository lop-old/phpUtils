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
		$key = \mb_strtolower($key);
		self::$config[$key] = $value;
	}
	public static function get($key) {
		$key = \mb_strtolower($key);
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
		if (!isset(self::$config['render type'])) {
			return NULL;
		}
		return self::$config['render type'];
	}
	public static function usingRenderType() {
		$type = self::getRenderType();
		if ($type == NULL) {
			return self::DEFAULT_RENDER_TYPE;
		}
		return $type;
	}



}
