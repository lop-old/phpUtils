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

final class San {
	private final function __construct() {}



	public static function AlphaNum($str) {
		return \preg_replace("/[^a-z0-9]+/i", '', $str);
	}
	public static function AlphaNumSafe($str) {
		return \preg_replace("/[^a-z0-9-._]+/i", '', $str);
	}
	public static function AlphaNumSpaces($str) {
		return \preg_replace("/[^\sa-z0-9-_]+/i", '', $str);
	}
	public static function AlphaNumFile($str) {
		$filter = '[:alnum:]\(\)\_\.\,\'\&\?\+\-\=\!';
		return \preg_replace('/[^'.$filter.']/', '', $str);
	}



}
