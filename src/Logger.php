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

\pxn\phpUtils\General::Init();

class Logger extends \Monolog\Logger {

	private static $loggers = [];



	public static function get($name='') {
		// default to last part of namespace
		if(empty($name)) {
			$trace = \debug_backtrace(FALSE, 2);
			$temp = \strrev($trace[1]['class']);
			unset($trace);
			Strings::grabPart($temp, '\\');
			$name = \strrev(Strings::grabPart($temp, '\\'));
			if(empty($name)) $name = '';
			unset($temp);
		}
		// new logger
		if(!isset(self::$loggers[$name]))
			self::$loggers[$name] = new static($name);
		return self::$loggers[$name];
	}
	public function __construct($name, array $handlers=[], array $processors=[]) {
		parent::__construct($name, $handlers, $processors);
	}



}
