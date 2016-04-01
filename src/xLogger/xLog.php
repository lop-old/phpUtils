<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger;

use pxn\phpUtils\xLogger\formatters\BasicFormat;

\pxn\phpUtils\General::Init();


class xLog extends xLogPrinting {

	const DEFAULT_LOGGER = '';

	private static $root = NULL;
	private static $loggers = [];
	private $DefaultFormatter = NULL;

	protected $name;
	protected $level;
	protected $parent;
	protected $formatter = NULL;
	protected $handlers = [];



	public static function getRoot() {
		if (self::$root == NULL)
			return self::get();
		return self::$root;
	}
	public static function get($name='') {
		if (empty($name) && self::$root != NULL)
			return self::$root;
		$name = self::ValidateName($name);
		if (isset(self::$loggers[$name])) {
			return self::$loggers[$name];
		}
		// new logger
//			$handler = new StreamHandler('php://stderr', Logger::DEBUG);
//			$formatter = new LineFormatter(
//					'[%datetime%] [%level_name%] [%channel%]  %message%  %context% %extra%'."\n",
//					'Y-m-d H:i:s',
//					FALSE,
//					TRUE
//			);
//			$handler->setFormatter($formatter);
//			$log->pushHandler($handler);
		$log = new self($name);
		self::$loggers[$name] = $log;
		return $log;
	}
	public static function set($name, $log) {
		$name = self::ValidateName($name);
		$existed = isset(self::$loggers[$name]) && self::$loggers[$name] != NULL;
		self::$loggers[$name] = $log;
		return $existed;
	}
	public function getWeak($name='') {
		$name = self::ValidateName($name);
		if (isset(self::$loggers[$name]))
			return self::$loggers[$name];
		return new self($name);
	}



	public static function ValidateName($name) {
		$name = \trim($name);

//TODO:

		if (empty($name))
			return self::DEFAULT_LOGGER;
		return $name;
	}



	public static function HandleBuffer() {
		\ob_start([
			self::getRoot(),
			'ProcessOB'
		]);
//		$func = function($buffer) {
//			$this->publish($buffer);
//if (empty($buffer))
//	return;
//$h = \fopen(__DIR__.'/test.222', 'a');
//\fwrite($h, $buffer."\n");
//\fclose($h);
//		};
//		\ob_start($func);
	}
	public function ProcessOB($buffer) {
		if (empty($buffer))
			return;
		$this->out($buffer);
	}



	public function __construct($name, $parent=NULL) {
		$this->name = self::ValidateName($name);
	}



	public function isRoot() {
		return (empty($this->name) && $this->parent == NULL);
	}



	public function setLevel($level) {
		$this->level = xLevel::FindLevel($level);
	}
	public function getLevel() {
		return $this->level;
	}
	public function isLoggable($level) {
		if ($level == NULL || $this->level == NULL)
			return TRUE;
		// force debug mode
//TODO:
//		if (xVars::debug())
//			return TRUE;
		if (xLevel::isLoggable($this->level, $level))
			return TRUE;
		return FALSE;
	}



	private function buildNameTree(&$list) {
		if ($this->parent != NULL) {
			$this->parent->buildNameTree($list);
			if (!empty($this->name))
				$list[] = $this->name;
		}
	}
	public function getNameTree() {
		return $this->buildNameTree($this);
	}



	public function addHandler($handler) {
		$this->handlers[] = $handler;
	}
	public function setHandler($handler) {
		$this->handlers = [ $handler ];
	}



	public function setFormatter(xLogFormatter $formatter) {
		$this->formatter = $formatter;
	}



	public function publish($msg='') {
		if ($msg instanceof xLogRecord) {
			// not loggable
//TODO:
//			if (!$msg->isLoggable($this->level))
//				return;
			$msg = $this->getFormatter()
				->getFormatted($msg);
		}
		foreach ($this->handlers as $handler) {
			$handler->write($msg);
		}
	}



	public function getFormatter() {
		// get from parent
		if ($this->parent != NULL) {
			$parentFormatter = $this->parent->getFormatter();
			if ($parentFormatter != NULL)
				return $parentFormatter;
		}
		// default formatter
		if($this->formatter == NULL) {
			if ($this->DefaultFormatter == NULL)
				$this->DefaultFormatter = new BasicFormat();
			return $this->DefaultFormatter;
		}
		// specific formatter
		return $this->formatter;
	}



//	public static function ValidateName($name) {
//		// default to class name
//		if (empty($name)) {
//			$trace = \debug_backtrace(FALSE, 3);
//			$str = $trace[2]['class'];
//			if ($str == 'ReflectionMethod') {
//				$str = $trace[1]['class'];
//			}
//			$pos = \strrpos($str, '\\');
//			$name = (
//				$pos === FALSE
//				? $str
//				: \substr($str, $pos+1)
//			);
//		}
//		if (empty($name)) $name = '';
//		return $name;
//	}



}
