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

use \pxn\phpUtils\General;

class GeneralTest extends \PHPUnit_Framework_TestCase {



	public function testTimestamp() {
		$a = General::getTimestamp();
		\usleep(10 * 1000); // sleep 10ms
		$b = General::getTimestamp();
		$c = $b - $a;
		// > 1
		$this->assertGreaterThan(1, $a);
		$this->assertGreaterThan(1, $b);
		// within 5-15ms
		$this->assertGreaterThan(0.005, $c);
		$this->assertLessThan(   0.015, $c);
	}



}
