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

/**
 * @coversDefaultClass \pxn\phpUtils\BasicEnum
 */
class BasicEnumTest extends \PHPUnit_Framework_TestCase {

	const EXAMPLE_CONSTANTS = [
			'DOG'     => 0,
			'CAT'     => 1,
			'FISH'    => 2,
			'PENGUIN' => 4,
			'LIZARD'  => 8
	];



	/**
	 * @covers ::getConstants
	 */
	public function testConstants() {
		// verify constants exist
		$this->assertEquals(
				print_r(self::EXAMPLE_CONSTANTS,          TRUE),
				print_r(BasicEnumExample::getConstants(), TRUE)
		);
	}



	/**
	 * @covers ::isValidName
	 * @covers ::getByName
	 */
	public function testByName() {
		// isValid functions
		$this->assertTrue (BasicEnumExample::isValidName('DOG'));
		$this->assertFalse(BasicEnumExample::isValidName('COW'));
		$this->assertTrue (BasicEnumExample::isValidName('DOG', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('COW', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('Dog', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('Cow', TRUE));
		$this->assertTrue (BasicEnumExample::isValidName('Dog', FALSE));
		$this->assertFalse(BasicEnumExample::isValidName('Cow', FALSE));
		// getBy functions
		$this->assertEquals   (4, BasicEnumExample::getByName('PENGUIN'));
		$this->assertNotEquals(8, BasicEnumExample::getByName('PENGUIN'));
		$this->assertEquals(NULL, BasicEnumExample::getByName('Pen'    ));
		$this->assertEquals   (4, BasicEnumExample::getByName('PENGUIN', TRUE));
		$this->assertNotEquals(8, BasicEnumExample::getByName('PENGUIN', TRUE));
		$this->assertEquals(NULL, BasicEnumExample::getByName('Penguin', TRUE));
		$this->assertEquals(NULL, BasicEnumExample::getByName('Penguin', TRUE));
		$this->assertEquals   (4, BasicEnumExample::getByName('Penguin', FALSE));
		$this->assertNotEquals(8, BasicEnumExample::getByName('Penguin', FALSE));
		$this->assertEquals(NULL, BasicEnumExample::getByName('Pen',     FALSE));
	}



	/**
	 * @covers ::isValidValue
	 * @covers ::getByValue
	 */
	public function testByValue() {
		// isValid functions
		$this->assertTRUE (BasicEnumExample::isValidValue(2 ));
		$this->assertFalse(BasicEnumExample::isValidValue(16));
		// getBy functions
		$this->assertEquals   ('PENGUIN', BasicEnumExample::getByValue(4));
		$this->assertNotEquals('PENGUIN', BasicEnumExample::getByValue(8));
	}



}
