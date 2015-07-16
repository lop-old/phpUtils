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

/**
 * DEBUG     (100): Detailed debug information.
 * INFO      (200): Interesting events. Examples: User logs in, SQL logs.
 * NOTICE    (250): Normal but significant events.
 * WARNING   (300): Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
 * ERROR     (400): Runtime errors that do not require immediate action but should typically be logged and monitored.
 * CRITICAL  (500): Critical conditions. Example: Application component unavailable, unexpected exception.
 * ALERT     (550): Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
 * EMERGENCY (600): Emergency: system is unusable.
 */

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
