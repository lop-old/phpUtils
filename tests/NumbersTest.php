<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\Numbers;


/**
 * @coversDefaultClass \pxn\phpUtils\Numbers
 */
class NumbersTest extends \PHPUnit_Framework_TestCase {



	/**
	 * @covers ::isNumber
	 */
	public function testIsNumber() {
		$this->assertTrue (Numbers::isNumber('1'       ), "Value: '1'"       );
		$this->assertTrue (Numbers::isNumber('0'       ), "Value: '0'"       );
		$this->assertTrue (Numbers::isNumber('000'     ), "Value: '000'"     );
		$this->assertTrue (Numbers::isNumber('-1'      ), "Value: '-1'"      );
		$this->assertTrue (Numbers::isNumber(' 1 '     ), "Value: ' 1 '"     );
		$this->assertFalse(Numbers::isNumber(' - 1 '   ), "Value: ' - 1 '"   );
		$this->assertFalse(Numbers::isNumber('1a'      ), "Value: '1a'"      );
		$this->assertFalse(Numbers::isNumber('a1'      ), "Value: 'a1'"      );
		$this->assertFalse(Numbers::isNumber('1 a'     ), "Value: '1 a'"     );
		$this->assertFalse(Numbers::isNumber('a'       ), "Value: 'a'"       );
		$this->assertTrue (Numbers::isNumber('99999999'), "Value: '99999999'");
		$this->assertTrue (Numbers::isNumber('007'     ), "Value: '007'"     );
		$this->assertFalse(Numbers::isNumber('0x5F12'  ), "Value: '0x5F12'"  );
	}



	##########
	## Math ##
	##########



	/**
	 * @covers ::MinMax
	 */
	public function testMinMax() {
		$this->assertEquals(999.9, Numbers::MinMax( 999.9));
		// min/max
		$this->assertEquals( 1, Numbers::MinMax( 999.9, -1, 1));
		$this->assertEquals(-1, Numbers::MinMax(-999.9, -1, 1));
		// min only
		$this->assertEquals(999.9, Numbers::MinMax( 999.9, 1));
		$this->assertEquals(    1, Numbers::MinMax(-999.9, 1));
		// max only
		$this->assertEquals(     1, Numbers::MinMax( 999.9, FALSE, 1));
		$this->assertEquals(-999.9, Numbers::MinMax(-999.9, FALSE, 1));
	}



	############
	## Format ##
	############



	/**
	 * @covers ::Round
	 */
	public function testRound() {
		$this->assertEquals('123.00', Numbers::Round(123,     2));
		$this->assertEquals('123.45', Numbers::Round(123.45,  2));
		$this->assertEquals('123.46', Numbers::Round(123.456, 2));
		$this->assertEquals('123.4',  Numbers::Round(123.44,  1));
		$this->assertEquals('123.5',  Numbers::Round(123.45,  1));
		$this->assertEquals('130',    Numbers::Round(125.6,  -1));
	}
	/**
	 * @covers ::Floor
	 */
	public function testFloor() {
		$this->assertEquals('123.00', Numbers::Floor(123,     2));
		$this->assertEquals('123.45', Numbers::Floor(123.45,  2));
		$this->assertEquals('123.45', Numbers::Floor(123.456, 2));
		$this->assertEquals('123.4',  Numbers::Floor(123.44,  1));
		$this->assertEquals('123.4',  Numbers::Floor(123.45,  1));
		$this->assertEquals('120',    Numbers::Floor(125.6,  -1));
	}
	/**
	 * @covers ::Ceil
	 */
	public function testCeil() {
		$this->assertEquals('123.00', Numbers::Ceil(123,      2));
		$this->assertEquals('123.45', Numbers::Ceil(123.45,   2));
		$this->assertEquals('123.46', Numbers::Ceil(123.456,  2));
		$this->assertEquals('123.5',  Numbers::Ceil(123.44,   1));
		$this->assertEquals('123.5',  Numbers::Ceil(123.45,   1));
		$this->assertEquals('130',    Numbers::Ceil(125.6,   -1));
	}
	/**
	 * @covers ::PadZeros
	 */
	public function testPadZeros() {
		$this->assertEquals('1',     Numbers::PadZeros(1,     0));
		$this->assertEquals('1.000', Numbers::PadZeros(1,     3));
		$this->assertEquals('1.200', Numbers::PadZeros(1.2,   3));
		$this->assertEquals('1.234', Numbers::PadZeros(1.234, 3));
		$this->assertEquals('1.234', Numbers::PadZeros(1.234, 2));
	}



	/**
	 * @covers ::FormatBytes
	 */
	public function testFormatBytes() {
		$this->assertEquals('1B',     Numbers::FormatBytes( 1    ));
		$this->assertEquals('1KB',    Numbers::FormatBytes( 1024 ));
		$this->assertEquals('1.01KB', Numbers::FormatBytes( 1030 ));
		$this->assertEquals('2MB',    Numbers::FormatBytes( 1024 * 1024 * 2 ));
		$this->assertEquals('3GB',    Numbers::FormatBytes( 1024 * 1024 * 1024 * 3 ));
		$this->assertEquals('4TB',    Numbers::FormatBytes( 1024 * 1024 * 1024 * 1024 * 4 ));
		$this->assertEquals('1B',     Numbers::FormatBytes(' 1 B '));
		$this->assertEquals('1KB',    Numbers::FormatBytes('1 K B'));
		$this->assertEquals('1KB',    Numbers::FormatBytes('1024B'));
		$this->assertEquals('1.01KB', Numbers::FormatBytes('1030B'));
		$this->assertEquals('2MB',    Numbers::FormatBytes('2048K'));
		$this->assertEquals('3GB',    Numbers::FormatBytes('3072M'));
		$this->assertEquals('4TB',    Numbers::FormatBytes('4096G'));
		$this->assertEquals('6TB',    Numbers::FormatBytes('6T'));
	}



	/**
	 * @covers ::FormatRoman
	 */
	public function testFormatRoman() {
		$this->assertEquals('I',    Numbers::FormatRoman(1   ));
		$this->assertEquals('II',   Numbers::FormatRoman(2   ));
		$this->assertEquals('III',  Numbers::FormatRoman(3   ));
		$this->assertEquals('IV',   Numbers::FormatRoman(4   ));
		$this->assertEquals('V',    Numbers::FormatRoman(5   ));
		$this->assertEquals('VI',   Numbers::FormatRoman(6   ));
		$this->assertEquals('VII',  Numbers::FormatRoman(7   ));
		$this->assertEquals('VIII', Numbers::FormatRoman(8   ));
		$this->assertEquals('IX',   Numbers::FormatRoman(9   ));
		$this->assertEquals('X',    Numbers::FormatRoman(10  ));
		$this->assertEquals('XI',   Numbers::FormatRoman(11  ));
		$this->assertEquals('XV',   Numbers::FormatRoman(15  ));
		$this->assertEquals('XVI',  Numbers::FormatRoman(16  ));
		$this->assertEquals('XXII', Numbers::FormatRoman(22  ));
		$this->assertEquals('XLII', Numbers::FormatRoman(42  ));
		$this->assertEquals('LIII', Numbers::FormatRoman(53  ));
		$this->assertEquals('XCI',  Numbers::FormatRoman(91  ));
		$this->assertEquals('CIV',  Numbers::FormatRoman(104 ));
		$this->assertEquals('CLV',  Numbers::FormatRoman(155 ));
		$this->assertEquals('CD',   Numbers::FormatRoman(400 ));
		$this->assertEquals('D',    Numbers::FormatRoman(500 ));
		$this->assertEquals('DC',   Numbers::FormatRoman(600 ));
		$this->assertEquals('CM',   Numbers::FormatRoman(900 ));
		$this->assertEquals('M',    Numbers::FormatRoman(1000));
		$this->assertEquals('MCCXXXIV', Numbers::FormatRoman(1234));
		$this->assertEquals('-20',Numbers::FormatRoman(-20));
		$this->assertEquals('11', Numbers::FormatRoman(11, 10));
		$this->assertEquals('IX', Numbers::FormatRoman(9,  10));
	}



	##########
	## Time ##
	##########



	/**
	 * @covers ::StringToSeconds
	 */
	public function testStringToSeconds() {
		$this->assertEquals(0.001,    Numbers::StringToSeconds('1n'        ));
		$this->assertEquals(1,        Numbers::StringToSeconds('1s'        ));
		$this->assertEquals(42,       Numbers::StringToSeconds('42s'       ));
		$this->assertEquals(60,       Numbers::StringToSeconds('1m'        ));
		$this->assertEquals(62,       Numbers::StringToSeconds('1m 2s'     ));
		$this->assertEquals(4010,     Numbers::StringToSeconds('1h 5m 110s'));
		$this->assertEquals(18121,    Numbers::StringToSeconds('5h 2m 1s'  ));
		$this->assertEquals(432000,   Numbers::StringToSeconds('5d'        ));
		$this->assertEquals(1296000,  Numbers::StringToSeconds('2w 1d'     ));
		$this->assertEquals(2592000,  Numbers::StringToSeconds('1o'        ));
		$this->assertEquals(31536000, Numbers::StringToSeconds('1y'        ));
		$this->assertEquals(31536000, Numbers::StringToSeconds('1 Year'    ));
		$this->assertEquals(34822861, Numbers::StringToSeconds('1y 1o 1w 1d 1h 1m 1s'));
		$this->assertEquals(0,        Numbers::StringToSeconds('1b'));
		$this->assertEquals(1,        Numbers::StringToSeconds('1b 1s'));
		$this->assertEquals(1,        Numbers::StringToSeconds('1s 1b'));
	}



	/**
	 * @covers ::SecondsToString
	 */
	public function testSecondsToString() {
		$this->assertEquals('1s',        Numbers::SecondsToString(1       ));
		$this->assertEquals('42s',       Numbers::SecondsToString(42      ));
		$this->assertEquals('1m',        Numbers::SecondsToString(60      ));
		$this->assertEquals('1m 2s',     Numbers::SecondsToString(62      ));
		$this->assertEquals('1h 6m 50s', Numbers::SecondsToString(4010    ));
		$this->assertEquals('5h 2m 1s',  Numbers::SecondsToString(18121   ));
		$this->assertEquals('5d',        Numbers::SecondsToString(432000  ));
		$this->assertEquals('15d',       Numbers::SecondsToString(1296000 ));
		$this->assertEquals('30d',       Numbers::SecondsToString(2592000 ));
		$this->assertEquals('1y',        Numbers::SecondsToString(31536000));
		$this->assertEquals('1y 38d 1h 1m 1s', Numbers::SecondsToString(34822861));
		$this->assertEquals('1 Second',        Numbers::SecondsToString(1,        FALSE));
		$this->assertEquals('2 Hours  2 Minutes', Numbers::SecondsToString(7320,  FALSE));
		$this->assertEquals('1 Year',          Numbers::SecondsToString(31536000, FALSE));
		$this->assertEquals('2 Years  2 Days', Numbers::SecondsToString(63244800, FALSE));
	}



}
