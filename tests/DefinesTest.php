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

use \pxn\phpUtils\Defines;

class DefinesTest extends \PHPUnit_Framework_TestCase {



	public function testValues() {
		$this->assertGreaterThanOrEqual(50600, Defines::PHP_MIN_VERSION);
		$this->assertLessThanOrEqual(   70000, Defines::PHP_MIN_VERSION);
		$this->assertEquals(Defines::DIR_SEP, \DIRECTORY_SEPARATOR);
		$this->assertEquals(Defines::NEWLINE, Defines::EOL);
		$this->assertEquals(Defines::TAB,     "\t");
		$this->assertEquals(Defines::S_QUOTE, '\'');
		$this->assertEquals(Defines::D_QUOTE, "\"");
		$this->assertEquals(Defines::INT_MAX, 2147483647);
		$this->assertEquals(Defines::INT_MIN,-2147483648);
		$this->assertEquals(Defines::NET_PORT_MAX, 65535);
		// number of seconds
		$this->assertEquals(Defines::S_MS,     0.001);
		$this->assertEquals(Defines::S_SECOND, 1);
		$this->assertEquals(Defines::S_MINUTE, 60);
		$this->assertEquals(Defines::S_HOUR,   3600);
		$this->assertEquals(Defines::S_DAY,    86400);
		$this->assertEquals(Defines::S_WEEK,   604800);
		$this->assertEquals(Defines::S_MONTH,  2592000);
		$this->assertEquals(Defines::S_YEAR,   31536000);
	}



}
