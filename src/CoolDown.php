<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class CoolDown {

	public $duration = 1.0;
	public $last = Defines::INT_MIN;



	public function __construct($duration) {
		$this->duration = (double) $duration;
	}



	public function runAgain() {
		$current = self::getTimestamp();
		// first run
		if ($this->last < 0.0) {
			$this->last = $current;
			return TRUE;
		}
		// run again
		if ($current - $this->last >= $this->duration) {
			$this->last = $current;
			return TRUE;
		}
		// cooling
		return FALSE;
	}



	public function getTimeSince() {
		if ($this->last < 0.0) {
			return -1.0;
		}
		return self::getTimestamp() - $this->last;
	}
	public function getTimeUntil() {
		if ($this->last < 0.0) {
			return -1.0;
		}
		return ($this->last + $this->duration) - self::getTimestamp();
	}



	public function reset() {
		$this->last = Defines::INT_MIN;
	}
	public function resetRun() {
		$this->last = self::getTimestamp();
	}



	public static function getTimestamp() {
		return General::getTimestamp();
	}



}
