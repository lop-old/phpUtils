<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples;

use pxn\phpUtils\BasicEnum;


class BasicEnumExample extends BasicEnum {

	const DOG     = 'woof';
	const CAT     = 'meow';
	const FISH    = 'bloop';
	const PENGUIN = 'sqeuaaaa';
	const BIRD    = 'churp';



	public static function getConstants() {
		return parent::getConstants();
	}



}
