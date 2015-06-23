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

use \pxn\phpUtils\Strings;

class StringsTest extends \PHPUnit_Framework_TestCase {

	const TRIM_TEST_DATA  = ' --   == test ==   -- ';



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



}
