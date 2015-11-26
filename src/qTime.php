<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class qTime {

	protected $start = Defines::INT_MIN;
	protected $last  = Defines::INT_MIN;

	protected static $global = NULL;



	public static function getGlobal() {
		if(self::$global == NULL)
			self::$global = new static(TRUE);
		return self::$global;
	}
	public static function getGlobalSinceStart() {
		return self::getGlobal()
				->getTimeSinceStart();
	}
	public static function getGlobalSinceLast() {
		return self::getGlobal()
				->getTimeSinceLast();
	}



	public function __construct($startNow=TRUE) {
		if($startNow)
			$this->Start();
	}



	public function Start() {
		if($this->start == Defines::INT_MIN)
			$this->start = self::getTimestamp();
	}
	public function Reset() {
		$this->start = self::getTimestamp();
	}



	public function getTimeSinceStart() {
		$now   = self::getTimestamp();
		$start = $this->start;
		if($start == Defines::INT_MIN)
			return FALSE;
		if($start > $now)
			return 0.0;
		return $now - $start;
	}
	public function getTimeSinceLast() {
		$now   = self::getTimestamp();
		$start = $this->start;
		$last  = $this->last;
		if($last == Defines::INT_MIN) {
			if($start == Defines::INT_MIN)
				return FALSE;
			$last = $start;
		}
		if($last > $now)
			return 0.0;
		$since = $now - $last;
		$this->last = $now;
		return $since;
	}



	public static function getTimestamp() {
		return General::getTimestamp();
	}



}
