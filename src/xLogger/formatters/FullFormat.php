<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger\formatters;

use pxn\phpUtils\xLogger\xLogFormatter;
use pxn\phpUtils\xLogger\xLogRecord;
use pxn\phpUtils\Strings;


class FullFormat implements xLogFormatter {

	const DATE_FORMAT = 'Y-m-d H:i:s';
	const LEVEL_PAD   = 7;

	protected $datetime;



	public function __construct() {
		$this->datetime = new \DateTime();
	}



	public function getFormatted(xLogRecord $record) {
		$msg = &$record->msg;
		$msg = \str_replace("\r", '', $msg);
		if ($record->msg == NULL) {
			$record->msg = '<NULL>';
		}
		if (empty($record->msg)) {
			return '';
		}
		$date  = $this->datetime->format(self::DATE_FORMAT);
		$level = $record->getLevelFormatted();
		$level = Strings::PadCenter($level, self::LEVEL_PAD);
		$msg = " {$date} [{$level}]  {$record->msg}";
		return \explode("\n", $msg);
	}



}
