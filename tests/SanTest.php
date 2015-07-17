<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\San;

/**
 * @coversDefaultClass \pxn\phpUtils\San
 */
class SanTest extends \PHPUnit_Framework_TestCase {

	const TEST_DATA = 'abcd ABCD 1234 -_=+ ,.?! @#$%^&*~ ()<>[]{};:`\'" \\|/';



	/**
	 * @covers ::AlphaNum
	 */
	public function testAlphaNum() {
		$this->assertEquals('abcdABCD1234', San::AlphaNum(self::TEST_DATA));
	}
	/**
	 * @covers ::AlphaNumSafe
	 */
	public function testAlphaNumSafe() {
		$this->assertEquals('abcdABCD1234-_', San::AlphaNumSafe(self::TEST_DATA));
	}
	/**
	 * @covers ::AlphaNumSpaces
	 */
	public function testAlphaNumSpaces() {
		$this->assertEquals('abcd ABCD 1234 -_    ', San::AlphaNumSpaces(self::TEST_DATA));
	}
	/**
	 * @covers ::AlphaNumFile
	 */
	public function testAlphaNumFile() {
		$this->assertEquals('abcdABCD1234-_=+,.?!&()\'', San::AlphaNumFile(self::TEST_DATA));
	}



}
