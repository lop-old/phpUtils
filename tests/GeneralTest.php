<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\General;


/**
 * @coversDefaultClass \pxn\phpUtils\General
 */
class GeneralTest extends \PHPUnit\Framework\TestCase {

	const EXPECTED_CLASS_STRING = 'pxn\\phpUtils\\tests\\GeneralTest';



	public function testArray() {
		$this->assertEmpty([]);
	}



	public function testClassName() {
		$this->assertEquals(
			self::EXPECTED_CLASS_STRING,
			\get_class($this)
		);
	}



	public function testTimestamp() {
		// all timings are in ms
		$this->PerformTimestampTest(
			10, // sleep time
			8,  // min expected time
			30  // max expected time
		);
	}
	/**
	 * @covers ::getTimestamp
	 * @covers ::Sleep
	 */
	private function PerformTimestampTest($sleepTime, $minExpected, $maxExpected) {
		$a = General::getTimestamp();
		General::Sleep( (double)$sleepTime );
		$b = General::getTimestamp();
		$c = $b - $a;
		// > 1
		$this->assertGreaterThan(1.0, $a);
		$this->assertGreaterThan(1.0, $b);
		// within 5-15ms
		$this->assertGreaterThan( ((double)$minExpected) / 1000.0, $c);
		$this->assertLessThan(    ((double)$maxExpected) / 1000.0, $c);
	}



	/**
	 * @covers ::GDSupported
	 */
	public function testGDSupported() {
		$this->assertEquals(
			\function_exists('imagepng'),
			General::GDSupported()
		);
	}



	/**
	 * @covers ::InstanceOfClass
	 */
	public function testInstanceOfClass() {
		$this->assertTrue(
			General::InstanceOfClass(
				self::EXPECTED_CLASS_STRING,
				$this
			)
		);
	}



	/**
	 * @covers ::ValidateClass
	 */
	public function testValidateClass() {
//TODO: needs assert "This test did not perform any assertions"
		General::ValidateClass(
			self::EXPECTED_CLASS_STRING,
			$this
		);
	}
	/**
	 * @covers ::ValidateClass
	 */
	public function testValidateClass_NullString() {
		try {
			General::ValidateClass(
				NULL,
				$this
			);
		} catch (\InvalidArgumentException $e) {
			$this->assertEquals(
				'classname not defined',
				$e->getMessage()
			);
			return;
		}
		$this->assertTrue(FALSE, 'Failed to throw expected exception');
	}
	/**
	 * @covers ::ValidateClass
	 */
	public function testValidateClass_BlankString() {
		try {
			General::ValidateClass(
				'',
				$this
			);
		} catch (\InvalidArgumentException $e) {
			$this->assertEquals(
				'classname not defined',
				$e->getMessage()
			);
			return;
		}
		$this->assertTrue(FALSE, 'Failed to throw expected exception');
	}
	/**
	 * @covers ::ValidateClass
	 */
	public function testValidateClass_NullObject() {
		try {
			General::ValidateClass(
				self::EXPECTED_CLASS_STRING,
				NULL
			);
		} catch (\InvalidArgumentException $e) {
			$this->assertEquals(
				'object not defined',
				$e->getMessage()
			);
			return;
		}
		$this->assertTrue(FALSE, 'Failed to throw expected exception');
	}
	/**
	 * @covers ::ValidateClass
	 */
	public function testValidateClass_ClassType() {
		try {
			General::ValidateClass(
				self::EXPECTED_CLASS_STRING.'_invalid',
				$this
			);
		} catch (\InvalidArgumentException $e) {
			$this->assertEquals(
				'Class object isn\'t of type '.
					self::EXPECTED_CLASS_STRING.'_invalid',
				$e->getMessage()
			);
			return;
		}
		$this->assertTrue(FALSE, 'Failed to throw expected exception');
	}



}
