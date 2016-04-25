<?php
/*
 * PoiXson phpUtils - Website Utilities Library
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
		self::$config['is shell'] = (
			isset($_SERVER['SHELL']) && ! empty($_SERVER['SHELL'])
		);
	}



	public static function isShell() {
		return self::$config['is shell'];
	}



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
