<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger\formatters;

use pxn\phpUtils\xLogger\xLogRecord;


class BasicFormat implements xLogFormatter {



	public function getFormatted(xLogRecord $record) {
		$msg = &$record->msg;
		$msg = \str_replace("\r", '', $msg);
		if ($record->msg == NULL)
			$record->msg = '<NULL>';
		$msg = "{$record->msg}";
		return \explode("\n", $msg);
	}



}
