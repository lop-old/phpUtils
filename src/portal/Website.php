<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;


abstract class Website {

	private static $instance = NULL;



//	public static function get() {
//		if(self::$instance == NULL)
//			self::$instance = new self();
//		return self::$instance;
//	}
	public static function peak() {
		return self::$instance;
	}
	public function __construct() {
		
	}



	public abstract function Render();



}
