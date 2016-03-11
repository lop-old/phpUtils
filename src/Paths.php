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
	protected static $local_base  = '';
	protected static $local_site  = '';

	// web paths
	protected static $web_base   = '';
	protected static $web_images = '';



	public static function init() {
		// local paths
//		$paths['local']['entry'] = $_SERVER['DOCUMENT_ROOT'];
		self::$local_pwd   = \getcwd();
		self::$local_base  = __DIR__;
		// web paths
		self::$web_images = '/static';
	}



	public static function pwd() {
		return self::$local_pwd;
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
		return self::base().'/tmp';
	}



}
