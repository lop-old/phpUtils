<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger;

use pxn\phpUtils\xLogger\xLogFormatter;
use pxn\phpUtils\Strings;


abstract class xLogPrinting {

	protected $formatter;



	public function __construct(xLogFormatter $formatter) {
		$this->formatter = $formatter;
	}



	public abstract function publish($msg='');



	public function title($msg) {
		if (!\is_array($msg)) {
			return $this->title( [ $title ] );
		}
		$len = 0;
		foreach ($msg as $m) {
			$size = \strlen($m);
			if ($size > $len)
				$len = $size;
		}
		$topbottom = \str_repeat('*', $len);
		$this->publish();
		$this->publish(" ***{$topbottom}*** ");
		foreach ($msg as $m) {
			$line = Strings::PadCenter(
				$m,
				$len,
				' '
			);
			$this->publish(" ** {$line} ** ");
		}
		$this->publish(" ***{$topbottom}*** ");
		$this->publish();
	}



	public function trace($e) {
//TODO:
fail ('trace() function not finished!!! '.__LINE__.' '.__FILE__);
	}



	public function out($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::STDOUT,
				$msg
			)
		);
	}
	public function err($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::STDERR,
				$msg
			)
		);
	}



	public function finest($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::FINEST,
				$msg
			)
		);
	}
	public function finer($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::FINER,
				$msg
			)
		);
	}
	public function fine($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::FINE,
				$msg
			)
		);
	}
	public function stats($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::STATS,
				$msg
			)
		);
	}
	public function info($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::INFO,
				$msg
			)
		);
	}
	public function warning($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::WARNING,
				$msg
			)
		);
	}
	public function notice($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::NOTICE,
				$msg
			)
		);
	}
	public function severe($msg='') {
		$this->publish(
			new xLogRecord(
				xLevel::SEVERE,
				$msg
			)
		);
	}
	public function fatal($msg='') {
		$this->publish (
			new xLogRecord(
				xLevel::FATAL,
				$msg
			)
		);
	}



}
