<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger;


class xLogRecord {

	public $msg;
	public $level;



	public function __construct($level=NULL, $msg='') {
		$this->level = $level;
		$this->msg   = $msg;
	}



	public function isLoggable($level) {
		return xLevel::isLoggable(
				$this->level,
				$level
		);
	}
	public function getLevelFormatted() {
		return xLevel::FindLevelName($this->level);
	}



}
