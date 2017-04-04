<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
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
class qTimeTest extends \PHPUnit\Framework\TestCase {



	public function testShort() {
		// times are in seconds
		$this->PerformTest(0.010);
	}
	public function testLonger() {
		// times are in seconds
		$this->PerformTest(0.500);
	}
	/**
	 * @covers ::getTimeSinceStart
	 * @covers ::getTimeSinceLast
	 * @covers \pxn\phpUtils\General::Sleep
	 */
	private function PerformTest($sleepTime) {
		// test global
		qTime::getGlobal()->Reset();
		$g = qTime::getGlobalSinceStart();
		$this->assertGreaterThanOrEqual(0.0, $g);
		$this->assertLessThan(          0.1, $g);
		unset($g);
		// test local instance
		$timer = new qTime(TRUE);
		$a = $timer->getTimeSinceStart();
		// wait a short while
		General::Sleep($sleepTime * 1000.0);
		$b = $timer->getTimeSinceLast();
		// wait a short while again
		General::Sleep($sleepTime * 1000.0);
		$c = $timer->getTimeSinceLast();
		// test time to start
		$this->assertGreaterThanOrEqual(0.0, $a);
		$this->assertLessThan(          0.1, $a);
		// test 1x sleep
		$this->assertGreaterThan($sleepTime * 0.9, $b);
		$this->assertLessThan(   $sleepTime * 1.9, $b);
		// test 2x sleep
		$this->assertGreaterThan($sleepTime * 0.9, $c);
		$this->assertLessThan(   $sleepTime * 1.9, $c);
		// test deviation
		$deviation = \abs($c - $b);
		$this->assertGreaterThanOrEqual(0.0, $deviation);
		$this->assertLessThan(          0.1, $deviation);
		// test overall time
		$finish = $timer->getTimeSinceStart();
		$this->assertGreaterThan($sleepTime * 1.9, $finish);
		$this->assertLessThan(   $sleepTime * 10.0, $finish);
		// test global
		qTime::getGlobalSinceLast();
		unset($timer, $a, $b, $c);
	}



}
