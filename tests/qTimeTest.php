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

/**
 * @coversDefaultClass \pxn\phpUtils\qTime
 */
class qTimeTest extends \PHPUnit_Framework_Testcase {



	public function testShort() {
	}
	/**
	 * @covers ::getTimeSinceStart
	 * @covers ::getTimeSinceLast
	 * @covers \pxn\phpUtils\General::Sleep
	 */
	private function PerformTest($sleepTime) {
		$q = new qTime();
		// test initial value
		$first = $q->getTimeSinceStart();
		$this->assertGreaterThanOrEqual(0.0, $first);
		$this->assertLessThan(          2.0, $first);
		// wait 10ms
		\usleep(10 * 1000);
		$a = $q->getTimeSinceStart();
		$b = $q->getTimeSinceLast();
		// wait another 10ms
		\usleep(10 * 1000);
		$c = $q->getTimeSinceLast();
		// overall time
		$overall = $q->getTimeSinceStart() - ($b + $c);
		// test all the things
		$this->assertGreaterThan(0.005, $a);
		$this->assertLessThan(   0.015, $b);
		$this->assertGreaterThan(0.005, $b);
		$this->assertLessThan(   0.015, $b);
		$this->assertLessThan(   0.005, $b - $a);
		$this->assertGreaterThan(0.005, $c);
		$this->assertLessThan(   0.015, $c);
		$this->assertLessThan(   0.005, $c - $b);
		$this->assertGreaterThanOrEqual(0.0, $overall);
		$this->assertLessThan(   0.005, $overall);
	}



}
