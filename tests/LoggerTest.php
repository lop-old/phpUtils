<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 * /
namespace pxn\phpUtils\tests;

use pxn\phpUtils\Logger;


/ **
 * @coversDefaultClass \pxn\phpUtils\Logger
 * /
class LoggerTest extends \PHPUnit\Framework\TestCase {



//	public function testValidateName() {
//		$expected = 'LoggerTest';
//		// null
//		$result = Logger::ValidateName(NULL);
//		$this->assertEquals($expected, $result);
//		// blank
//		$result = Logger::ValidateName('');
//		$this->assertEquals($expected, $result);
//		// string
//		$result = Logger::ValidateName('testname');
//		$this->assertEquals('testname', $result);
//	}



	public function testInstances() {
		$a = Logger::get('a');
		$b = Logger::get('b');
		$this->assertNotNull($a);
		$this->assertNotNull($b);
		$this->assertTrue($a !== $b);
		$this->assertTrue($a === Logger::get('a'));
		unset($a, $b);
	}



	public function testEmpty() {
		$a = Logger::get();
		$b = Logger::get('');
		$c = Logger::get(NULL);
		$this->assertTrue ($a === $b);
		$this->assertTrue ($a === $c);
		$this->assertFalse($a === Logger::get('test'));
	}



	public function testSet() {
		$this->assertFalse(Logger::get('a') === Logger::get('b'));
		Logger::set(
			'a',
			Logger::get('b')
		);
		$this->assertTrue (Logger::get('a') === Logger::get('b'));
	}



}
*/
