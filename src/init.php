<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


require('init_1_startup.php');

// defines
\pxn\phpUtils\Defines::init();

// paths
\pxn\phpUtils\Paths::init();

require('init_2_functions.php');

require('init_3_debug.php');

\pxn\phpUtils\Config::init();


final class init {
	public static function init() {}
}
