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

use pxn\phpUtils\qTime;
use pxn\phpUtils\General;

/**
 * @coversDefaultClass \pxn\phpUtils\qTime
 */
class qTimeTest extends \PHPUnit_Framework_Testcase {



	public function testShort() {
		// times are in seconds
		$this->PerformTest(0.010);
	}
	public function testLonger() {
		// times are in seconds
		$this->PerformTest(0.5);
	}
	/**
	 * @covers ::getTimeSinceStart
	 * @covers ::getTimeSinceLast
	 * @covers \pxn\phpUtils\General::Sleep
	 */
	private function PerformTest($sleepTime) {
		$q = new qTime();
		$a = $q->getTimeSinceStart();
		// wait a short while
		General::Sleep($sleepTime * 1000.0);
		$b = $q->getTimeSinceLast();
		// wait a short while again
		General::Sleep($sleepTime * 1000.0);
		$c = $q->getTimeSinceLast();
		// test time to start
		$this->assertGreaterThanOrEqual(0.0, $a);
		$this->assertLessThan(          0.1, $a);
		// test 1x sleep
		$this->assertGreaterThan($sleepTime * 0.8, $b);
		$this->assertLessThan(   $sleepTime * 1.5, $b);
		// test 2x sleep
		$this->assertGreaterThan($sleepTime * 0.8, $c);
		$this->assertLessThan(   $sleepTime * 1.5, $c);
		// test deviation
		$deviation = \abs($c - $b);
		$this->assertGreaterThanOrEqual(0.0, $deviation);
		$this->assertLessThan(          0.1, $deviation);
		// test overall time
		$finish = $q->getTimeSinceStart();
		$this->assertGreaterThan($sleepTime * 1.8, $finish);
		$this->assertLessThan(   $sleepTime * 10.0, $finish);
		unset($a, $b, $c);
	}



}
