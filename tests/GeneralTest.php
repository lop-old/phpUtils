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

use pxn\phpUtils\General;

class GeneralTest extends \PHPUnit_Framework_TestCase {



	public function testTimestamp() {
		// all timings are in ms
		$this->PerformTimestampTest(
				10, // sleep time
				8,  // min expected time
				30  // max expected time
		);
	}
	private function PerformTimestampTest($sleepTime, $minExpected, $maxExpected) {
		$a = General::getTimestamp();
		\usleep($sleepTime * 1000);
		$b = General::getTimestamp();
		$c = $b - $a;
		// > 1
		$this->assertGreaterThan(1, $a);
		$this->assertGreaterThan(1, $b);
		// within 5-15ms
		$this->assertGreaterThan($minExpected / 1000, $c);
		$this->assertLessThan(   $maxExpected / 1000, $c);
	}



}
