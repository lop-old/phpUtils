<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
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

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

\pxn\phpUtils\General::Init();

class Logger extends \Monolog\Logger {

	private static $loggers = [];



	public static function get($name='') {
		$name = self::ValidateName($name);
		// new logger
		if(!isset(self::$loggers[$name])) {
			$log = new self($name);
			self::$loggers[$name] = $log;
			$handler = new StreamHandler('php://stderr', Logger::DEBUG);
			$formatter = new LineFormatter(
					'[%datetime%] [%channel%|%level_name%]  %message%  %context% %extra%'."\n",
					'Y-m-d H:i:s',
					FALSE,
					TRUE
			);
			$handler->setFormatter($formatter);
			$log->pushHandler($handler);
		}
		return self::$loggers[$name];
	}
	public static function set($name, $logger) {
		$name = self::ValidateName($name);
		$existed = isset(self::$loggers[$name]);
		self::$loggers[$name] = $logger;
		return $existed;
	}
	public function __construct($name, array $handlers=[], array $processors=[]) {
		parent::__construct($name, $handlers, $processors);
	}



	public static function ValidateName($name) {
		// default to class name
		if(empty($name)) {
			$trace = \debug_backtrace(FALSE, 3);
			$str = $trace[2]['class'];
			if($str == 'ReflectionMethod')
				$str = $trace[1]['class'];
			$pos = \strrpos($str, '\\');
			$name = ($pos === FALSE ? $str : \substr($str, $pos+1));
		}
		if(empty($name)) $name = '';
		return $name;
	}



}
