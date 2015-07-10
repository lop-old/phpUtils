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



	const TRIM_TEST_DATA = "\t--   == test ==   --\t";



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
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '-', '=', "\t")
		);
		$this->assertEquals(
				'test',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '--', '==', "\t")
		);
		$this->assertEquals(
				'--   == test ==   --',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', '=', "\t")
		);
		$this->assertEquals(
				'test',
				Strings::Trim(self::TRIM_TEST_DATA, ' ', ['--', '=='], "\t")
		);
		$this->assertEquals(
				'123',
				Strings::Trim('01230', '0')
		);
		$this->assertEquals(
				'',
				Strings::Trim('01230', '0', '1', '2', '3')
		);
	}
	/**
	 * @covers ::TrimFront
	 */
	public function testTrimFront() {
		$this->assertEquals(
				"--   == test ==   --\t",
				Strings::TrimFront(self::TRIM_TEST_DATA)
		);
		$this->assertEquals(
				"test ==   --\t",
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '-', '=', "\t")
		);
		$this->assertEquals(
				"test ==   --\t",
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '--', '==', "\t")
		);
		$this->assertEquals(
				"--   == test ==   --\t",
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', '=', "\t")
		);
		$this->assertEquals(
				"test ==   --\t",
				Strings::TrimFront(self::TRIM_TEST_DATA, ' ', ['--', '=='], "\t")
		);
		$this->assertEquals(
				'1230',
				Strings::TrimFront('01230', '0')
		);
		$this->assertEquals(
				'',
				Strings::TrimFront('01230', '0', '1', '2', '3')
		);
	}
	/**
	 * @covers ::TrimEnd
	 */
	public function testTrimEnd() {
		$this->assertEquals(
				"\t--   == test ==   --",
				Strings::TrimEnd(self::TRIM_TEST_DATA)
		);
		$this->assertEquals(
				"\t--   == test",
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '-', '=', "\t")
		);
		$this->assertEquals(
				"\t--   == test",
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '--', '==', "\t")
		);
		$this->assertEquals(
				"\t--   == test ==   --",
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', '=', "\t")
		);
		$this->assertEquals(
				"\t--   == test",
				Strings::TrimEnd(self::TRIM_TEST_DATA, ' ', ['--', '=='], "\t")
		);
		$this->assertEquals(
				'0123',
				Strings::TrimEnd('01230', '0')
		);
		$this->assertEquals(
				'',
				Strings::TrimEnd('01230', '0', '1', '2', '3')
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
		// blank strings
		$this->assertEquals('', Strings::TrimQuotes('""'));
		$this->assertEquals('', Strings::TrimQuotes("''"));
		$this->assertEquals('', Strings::TrimQuotes('``'));
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



	##############
	## Contains ##
	##############



	/**
	 * @covers ::Contains
	 */
	public function testContains() {
		$this->assertTrue( Strings::Contains(self::TRIM_TEST_DATA, 'test'));
		$this->assertTrue( Strings::Contains(self::TRIM_TEST_DATA, 'Test', TRUE ));
		$this->assertFalse(Strings::Contains(self::TRIM_TEST_DATA, 'Test', FALSE));
	}



	##############
	## Get Part ##
	##############



	const PART_TEST_DATA  = "aaa bbb  ccc\tddd";



	public function test_findPart() {
		$data = self::PART_TEST_DATA;
		// find space
		$result = Strings::findPart($data, ' ');
		$this->assertTrue(\is_array($result));
		$this->assertEquals(3,   $result['POS']);
		$this->assertEquals(' ', $result['PAT']);
		// find double-space
		$result = Strings::findPart($data, '  ');
		$this->assertTrue(\is_array($result));
		$this->assertEquals(7,    $result['POS']);
		$this->assertEquals('  ', $result['PAT']);
		// find tab
		$result = Strings::findPart($data, "\t");
		$this->assertTrue(\is_array($result));
		$this->assertEquals(12,   $result['POS']);
		$this->assertEquals("\t", $result['PAT']);
		// find nothing
		$result = Strings::findPart($data, '-');
		$this->assertFalse(\is_array($result));
		$this->assertEquals(NULL, $result);
		unset($data, $result);
	}
	public function test_peakPart_grapPart() {
		$data = self::PART_TEST_DATA;
		// aaa
		$this->assertEquals('aaa', Strings::peakPart($data, ' '));
		$this->assertEquals('aaa', Strings::grabPart($data, ' '));
		$this->assertEquals("bbb  ccc\tddd", $data);
		// bbb
		$this->assertEquals('bbb', Strings::peakPart($data, ' '));
		$this->assertEquals('bbb', Strings::grabPart($data, ' '));
		$this->assertEquals("ccc\tddd", $data);
		// ccc
		$this->assertEquals("ccc\tddd", Strings::peakPart($data, ' '));
		$this->assertEquals('ccc',      Strings::peakPart($data, [' ', "\t"]));
		$this->assertEquals('ccc',      Strings::grabPart($data, [' ', "\t"]));
		$this->assertEquals('ddd', $data);
		// ddd
		$this->assertEquals('ddd', Strings::peakPart($data, ' '));
		$this->assertEquals('ddd', Strings::grabPart($data, ' '));
		$this->assertEquals('', $data);
		unset($data);
	}



	################
	## File Paths ##
	################



	public function testBuildPath() {
		$this->assertEquals( 'home/user/Desktop',  Strings::BuildPath(     'home',   'user',   'Desktop'     ));
		$this->assertEquals('/home/user/Desktop',  Strings::BuildPath('/', 'home',   'user',   'Desktop'     ));
		$this->assertEquals('/home/user/Desktop/', Strings::BuildPath(    '/home',   'user',   'Desktop', '/'));
		$this->assertEquals('/home/user/Desktop/', Strings::BuildPath(    '/home/', '/user/', '/Desktop/'    ));
		$this->assertEquals('home', Strings::BuildPath('', 'home', ''));
	}



}
