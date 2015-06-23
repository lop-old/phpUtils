<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\Examples;

use pxn\phpUtils\BasicEnum;

class BasicEnumExample extends BasicEnum {


	const DOG     = 0;
	const CAT     = 1;
	const FISH    = 2;
	const PENGUIN = 4;
	const LIZARD  = 8;



	public static function getConstants() {
		return parent::getConstants();
	}



}
