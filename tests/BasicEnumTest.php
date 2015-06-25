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
			'DOG'     => 'woof',
			'CAT'     => 'meow',
			'FISH'    => 'bloop',
			'PENGUIN' => 'sqeuaaaa',
			'BIRD'    => 'churp'
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
		// isValidName
		$this->assertTrue (BasicEnumExample::isValidName('DOG'));
		$this->assertFalse(BasicEnumExample::isValidName('COW'));
		$this->assertTrue (BasicEnumExample::isValidName('DOG', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('COW', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('Dog', TRUE));
		$this->assertFalse(BasicEnumExample::isValidName('Cow', TRUE));
		$this->assertTrue (BasicEnumExample::isValidName('Dog', FALSE));
		$this->assertFalse(BasicEnumExample::isValidName('Cow', FALSE));
		// getByName functions
		$this->assertEquals('churp', BasicEnumExample::getByName('BIRD'));
		$this->assertEquals(NULL,    BasicEnumExample::getByName('Brd' ));
		$this->assertEquals('churp', BasicEnumExample::getByName('BIRD', TRUE));
		$this->assertEquals(NULL,    BasicEnumExample::getByName('Bird', TRUE));
		$this->assertEquals('churp', BasicEnumExample::getByName('Bird', FALSE));
		$this->assertEquals(NULL,    BasicEnumExample::getByName('Brd',  FALSE));
	}



	/**
	 * @covers ::isValidValue
	 * @covers ::getByValue
	 */
	public function testByValue() {
		// isValidValue
		$this->assertTrue (BasicEnumExample::isValidValue('woof'));
		$this->assertFalse(BasicEnumExample::isValidValue('moo' ));
		$this->assertTrue (BasicEnumExample::isValidValue('woof', TRUE));
		$this->assertFalse(BasicEnumExample::isValidValue('moo',  TRUE));
		$this->assertFalse(BasicEnumExample::isValidValue('Woof', TRUE));
		$this->assertFalse(BasicEnumExample::isValidValue('Moo',  TRUE));
		$this->assertTrue (BasicEnumExample::isValidValue('Woof', FALSE));
		$this->assertFalse(BasicEnumExample::isValidValue('Moo',  FALSE));
		// getByValue
		$this->assertEquals('CAT', BasicEnumExample::getByValue('meow'));
		$this->assertEquals(NULL,  BasicEnumExample::getByValue('mow'));
		$this->assertEquals('CAT', BasicEnumExample::getByValue('meow', TRUE));
		$this->assertEquals(NULL,  BasicEnumExample::getByValue('Meow', TRUE));
		$this->assertEquals('CAT', BasicEnumExample::getByValue('Meow', FALSE));
		$this->assertEquals(NULL,  BasicEnumExample::getByValue('mow',  FALSE));
	}



}
