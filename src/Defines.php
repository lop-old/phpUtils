<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class Defines {
	private final function __construct() {}
	public static function init() {}


	const PHP_MIN_VERSION = 50600;

	const DEBUG_COOKIE = '_debug';

	const DIR_SEP      = \DIRECTORY_SEPARATOR;
	const NEWLINE      = "\n";
	const CRLF         = self::NEWLINE;
	const EOL          = self::NEWLINE;
	const TAB          = "\t";
	const QUOTE_S      = '\'';
	const QUOTE_D      = "\"";
	const S_QUOTE      = self::QUOTE_S;
	const D_QUOTE      = self::QUOTE_D;
	const ACCENT       = '`';

	const INT_MAX      = 2147483647;
	const INT_MIN      =-2147483648;
	const NET_PORT_MAX = 65535;

	// number of seconds
	const S_MS      = 0.001;
	const S_SECOND  = 1;
	const S_MINUTE  = 60;
	const S_HOUR    = 3600;
	const S_DAY     = 86400;
	const S_WEEK    = 604800;
	const S_MONTH   = 2592000;
	const S_YEAR    = 31536000;

	// number of bytes
	const KB = 1024;
	const MB = 1048576;
	const GB = 1073741824;
	const TB = 1099511627776;


	public static function getDebugCookieName() {
		$website_name = '';
		$website = \pxn\phpUtils\portal\Website::peak();
		if ($website != NULL) {
			$website_name = $website->getName();
		}
		return \str_replace(
			'%website%',
			$website_name,
			self::DEBUG_COOKIE
		);
	}


}
