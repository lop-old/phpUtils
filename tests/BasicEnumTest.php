<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\examples\BasicEnumExample;


/**
 * @coversDefaultClass \pxn\phpUtils\BasicEnum
 */
class BasicEnumTest extends \PHPUnit\Framework\TestCase {

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
		$this->assertTrue ( BasicEnumExample::isValidName('DOG'       ) );
		$this->assertFalse( BasicEnumExample::isValidName('COW'       ) );
		$this->assertTrue ( BasicEnumExample::isValidName('DOG', FALSE) );
		$this->assertFalse( BasicEnumExample::isValidName('COW', FALSE) );
		$this->assertFalse( BasicEnumExample::isValidName('Dog', FALSE) );
		$this->assertFalse( BasicEnumExample::isValidName('Cow', FALSE) );
		$this->assertTrue ( BasicEnumExample::isValidName('Dog',  TRUE) );
		$this->assertFalse( BasicEnumExample::isValidName('Cow',  TRUE) );
		// getByName
		$this->assertEquals( 'churp', BasicEnumExample::getByName('BIRD'       ) );
		$this->assertEquals( NULL,    BasicEnumExample::getByName('Brd'        ) );
		$this->assertEquals( 'churp', BasicEnumExample::getByName('BIRD', FALSE) );
		$this->assertEquals( NULL,    BasicEnumExample::getByName('Bird', FALSE) );
		$this->assertEquals( 'churp', BasicEnumExample::getByName('Bird',  TRUE) );
		$this->assertEquals( NULL,    BasicEnumExample::getByName('Brd',   TRUE) );
	}



	/**
	 * @covers ::isValidValue
	 * @covers ::getByValue
	 */
	public function testByValue() {
		// isValidValue
		$this->assertTrue ( BasicEnumExample::isValidValue('woof'       ) );
		$this->assertFalse( BasicEnumExample::isValidValue('moo'        ) );
		$this->assertTrue ( BasicEnumExample::isValidValue('woof', FALSE) );
		$this->assertFalse( BasicEnumExample::isValidValue('moo',  FALSE) );
		$this->assertFalse( BasicEnumExample::isValidValue('Woof', FALSE) );
		$this->assertFalse( BasicEnumExample::isValidValue('Moo',  FALSE) );
		$this->assertTrue ( BasicEnumExample::isValidValue('Woof',  TRUE) );
		$this->assertFalse( BasicEnumExample::isValidValue('Moo',   TRUE) );
		// getByValue
		$this->assertEquals( 'CAT', BasicEnumExample::getByValue('meow'       ) );
		$this->assertEquals( NULL,  BasicEnumExample::getByValue('mow'        ) );
		$this->assertEquals( 'CAT', BasicEnumExample::getByValue('meow', FALSE) );
		$this->assertEquals( NULL,  BasicEnumExample::getByValue('Meow', FALSE) );
		$this->assertEquals( 'CAT', BasicEnumExample::getByValue('Meow',  TRUE) );
		$this->assertEquals( NULL,  BasicEnumExample::getByValue('mow',   TRUE) );
	}



}
