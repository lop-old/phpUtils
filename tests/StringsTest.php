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

use pxn\phpUtils\Strings;

/**
 * @coversDefaultClass \pxn\phpUtils\Strings
 */
class StringsTest extends \PHPUnit_Framework_TestCase {



	#################
	## Trim String ##
	#################



	const TRIM_TEST_DATA  = ' --   == test ==   -- ';



	/**
	 * @covers ::Trim
	 */
	public function testTrim() {
		$this->assertEquals(
				'--   == test ==   --',
				Strings::Trim(self::TRIM_TEST_DATA)
		);
		$this->assertEquals(
				'test',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '-', '=')
		);
		$this->assertEquals(
				'test',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '--', '==')
		);
		$this->assertEquals(
				'--   == test ==   --',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '=')
		);
	}
	/**
	 * @covers ::TrimFront
	 */
	public function testTrimFront() {
		$this->assertEquals(
				'--   == test ==   -- ',
				Strings::TrimFront(self::TRIM_TEST_DATA)
		);
		$this->assertEquals(
				'test ==   -- ',
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '-', '=')
		);
		$this->assertEquals(
				'test ==   -- ',
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '--', '==')
		);
		$this->assertEquals(
				'--   == test ==   -- ',
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '=')
		);
	}
	/**
	 * @covers ::TrimEnd
	 */
	public function testTrimEnd() {
		$this->assertEquals(
				' --   == test ==   --',
				Strings::TrimEnd(self::TRIM_TEST_DATA)
		);
		$this->assertEquals(
				' --   == test',
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '-', '=')
		);
		$this->assertEquals(
				' --   == test',
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '--', '==')
		);
		$this->assertEquals(
				' --   == test ==   --',
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '=')
		);
	}



	/**
	 * @covers ::TrimQuotes
	 */
	public function testTrimQuotes() {
		// matching quotes
		$this->assertEquals('test', Strings::TrimQuotes( '"test"' ));
		$this->assertEquals('test', Strings::TrimQuotes( "'test'" ));
		$this->assertEquals('test', Strings::TrimQuotes('``test``'));
		// mis-matched quotes
		$this->assertEquals(  'test"',  Strings::TrimQuotes(  '"test""'));
		$this->assertEquals( '"test',   Strings::TrimQuotes( '""test"' ));
		$this->assertEquals(  "test'",  Strings::TrimQuotes(   "test'" ));
		$this->assertEquals( "'test",   Strings::TrimQuotes(  "'test"  ));
		$this->assertEquals(  'test``', Strings::TrimQuotes(   'test``'));
		$this->assertEquals('``test',   Strings::TrimQuotes('"``test"' ));
	}



	######################
	## Starts/Ends With ##
	######################



	const STARTSENDS_DATA = 'abcdefg';



	/**
	 * @covers ::StartsWith
	 */
	public function testStartsWith() {
		// case-sensitive
		$this->assertTrue (Strings::StartsWith(self::STARTSENDS_DATA, 'abc', FALSE));
		$this->assertFalse(Strings::StartsWith(self::STARTSENDS_DATA, 'Abc', FALSE));
		$this->assertFalse(Strings::StartsWith(self::STARTSENDS_DATA, 'bcd', FALSE));
		$this->assertFalse(Strings::StartsWith(self::STARTSENDS_DATA, 'Bcd', FALSE));
		// ignore case
		$this->assertTrue (Strings::StartsWith(self::STARTSENDS_DATA, 'abc', TRUE));
		$this->assertTrue (Strings::StartsWith(self::STARTSENDS_DATA, 'Abc', TRUE));
		$this->assertFalse(Strings::StartsWith(self::STARTSENDS_DATA, 'bcd', TRUE));
		$this->assertFalse(Strings::StartsWith(self::STARTSENDS_DATA, 'Bcd', TRUE));
	}
	/**
	 * @covers ::EndsWith
	 */
	public function testEndsWith() {
		// case-sensitive
		$this->assertTrue (Strings::EndsWith(self::STARTSENDS_DATA, 'efg', FALSE));
		$this->assertFalse(Strings::EndsWith(self::STARTSENDS_DATA, 'Efg', FALSE));
		$this->assertFalse(Strings::EndsWith(self::STARTSENDS_DATA, 'def', FALSE));
		$this->assertFalse(Strings::EndsWith(self::STARTSENDS_DATA, 'Def', FALSE));
		// ignore case
		$this->assertTrue (Strings::EndsWith(self::STARTSENDS_DATA, 'efg', TRUE));
		$this->assertTrue (Strings::EndsWith(self::STARTSENDS_DATA, 'Efg', TRUE));
		$this->assertFalse(Strings::EndsWith(self::STARTSENDS_DATA, 'def', TRUE));
		$this->assertFalse(Strings::EndsWith(self::STARTSENDS_DATA, 'Def', TRUE));
	}



	#####################
	## Force Start/End ##
	#####################



	const FORCE_DATA   = 'test';
	const FORCE_APPEND = '123';



	/**
	 * @covers ::ForceStartsWith
	 */
	public function testForceStartsWith() {
		$this->assertEquals(
				self::FORCE_APPEND.self::FORCE_DATA,
				Strings::ForceStartsWith(
						self::FORCE_DATA,
						self::FORCE_APPEND
				)
		);
		$this->assertEquals(
				self::FORCE_APPEND.self::FORCE_DATA,
				Strings::ForceStartsWith(
						self::FORCE_APPEND.self::FORCE_DATA,
						self::FORCE_APPEND
				)
		);
	}
	/**
	 * @covers ::ForceEndsWith
	 */
	public function testForceEndsWith() {
		$this->assertEquals(
				self::FORCE_DATA.self::FORCE_APPEND,
				Strings::ForceEndsWith(
						self::FORCE_DATA,
						self::FORCE_APPEND
				)
		);
		$this->assertEquals(
				self::FORCE_DATA.self::FORCE_APPEND,
				Strings::ForceEndsWith(
						self::FORCE_DATA.self::FORCE_APPEND,
						self::FORCE_APPEND
				)
		);
	}



}
