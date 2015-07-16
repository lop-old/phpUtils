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

use pxn\phpUtils\Logger;

/**
 * @coversDefaultClass \pxn\phpUtils\Logger
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {



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



}
