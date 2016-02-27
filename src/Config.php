<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use \pxn\phpUtils\Paths;


final class Config {
	private function __construct() {}

	protected static $config = array();



	public static function init() {
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
