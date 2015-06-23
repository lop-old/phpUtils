<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\Defines;
use pxn\phpUtils\Examples\BasicEnumExample;

class BasicEnumTest extends \PHPUnit_Framework_TestCase {

	const EXAMPLE_CONSTANTS = [
			'DOG'     => 0,
			'CAT'     => 1,
			'FISH'    => 2,
			'PENGUIN' => 4,
			'LIZARD'  => 8
	];



	public function testConstants() {
		// verify constants exist
		$this->assertEquals(
				print_r(self::EXAMPLE_CONSTANTS,          TRUE),
				print_r(BasicEnumExample::getConstants(), TRUE)
		);
	}
	public function testNamesValues() {
		// isValid functions
		$this->assertTrue (BasicEnumExample::isValidName('DOG'));
		$this->assertFalse(BasicEnumExample::isValidName('COW'));
		$this->assertTRUE (BasicEnumExample::isValidValue(2 ));
		$this->assertFalse(BasicEnumExample::isValidValue(16));
		// getBy functions
		$this->assertEquals   (4, BasicEnumExample::getByName('PENGUIN'));
		$this->assertNotEquals(8, BasicEnumExample::getByName('PENGUIN'));
		$this->assertEquals   ('PENGUIN', BasicEnumExample::getByValue(4));
		$this->assertNotEquals('PENGUIN', BasicEnumExample::getByValue(8));
	}



}
