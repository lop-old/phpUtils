<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

class Logger extends \Monolog\Logger {

	private static $loggers = [];



	public static function get($name='') {
		if($name === NULL) $name = '';
		if(!isset(self::$loggers[$name]))
			self::$loggers[$name] = new static($name);
		return self::$loggers[$name];
	}
	public function __construct($name, array $handlers=[], array $processors=[]) {
		parent::__construct($name, $handlers, $processors);
	}



}
