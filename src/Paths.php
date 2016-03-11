<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class Paths {
	private function __construct() {}

	// local paths
	protected static $local_pwd = '';
	protected static $local_utils = '';
	protected static $local_base  = '';
	protected static $local_site  = '';

	// web paths
	protected static $web_base   = '';
	protected static $web_images = '';



	public static function init() {
		// local paths
//		$paths['local']['entry'] = $_SERVER['DOCUMENT_ROOT'];
		self::$local_pwd   = \getcwd();
		self::$local_utils = __DIR__;
		self::$local_base  = @$_SERVER['DOCUMENT_ROOT'];
		// get base path from backtrace (shell mode)
		if (empty(self::$local_base)) {
			$trace = \debug_backtrace();
			$last  = \end($trace);
			self::$local_base = \dirname($last['file']);
		}
		// web paths
		self::$web_images = '/static';
		// ensure all is good
		if (empty(self::$local_pwd)) {
			fail ('Failed to detect local pwd path!');
			exit(1);
		}
		if (empty(self::$local_utils)) {
			fail ('Failed to detect local utils path!');
			exit(1);
		}
		if (empty(self::$local_base)) {
			fail ('Failed to detect local base path!');
			exit(1);
		}
	}



	public static function pwd() {
		return self::$local_pwd;
	}
	public static function utils() {
		return self::$local_utils;
	}
	public static function base() {
		return self::$local_base;
	}
	public static function site() {
		// find site path
		if (empty(self::$local_site)) {
//self::$site = self::entry().DIR_SEP.
//  \psm\portal::get()->getWebsite()->siteName();
			$website = \pxn\phpUtils\portal\Website::peak();
			// website object not loaded yet
			if ($website === NULL) {
//TODO:
				echo '<p>Website object not loaded yet!</p>'.\CRLF;
				exit(1);
			}
//TODO:
return NULL;
//			$path = 
			$path = self::entry().\DIR_SEP.
				$website->getName();
				
			unset($path);
		}
		return self::$local_site;
	}



	public static function getTwigTempDir() {
		$path = self::base().'/tmp';
		if (!is_dir($path.'/')) {
			\mkdir($path, 0644);
		}
		return $path;
	}



}
