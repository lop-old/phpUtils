<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\console;


class ConsoleFactory {
	private function __construct() {}

	private static $instance = NULL;



	public static function get() {
		if (self::$instance != NULL)
			return self::$instance;
		self::$instance = new ConsoleApp();
		return self::$instance;
	}



}
