<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\System;
use pxn\phpUtils\Strings;

/**
 * @coversDefaultClass \pxn\phpUtils\System
 */
class SystemTest extends \PHPUnit_Framework_TestCase {

	const TEST_DIR1 = '_SystemTest_TEMP_/';
	const TEST_DIR2 = 'AnotherDir/';
	const TEST_FILE = 'testfilename.txt';



	/**
	 * @covers ::mkDir
	 * @covers ::rmDir
	 */
	public function test_mkDir_rmDir() {
		$cwd = $this->getCWD();
		// create test dirs
		System::mkDir($cwd,self::TEST_DIR1);
		System::mkDir($cwd.self::TEST_DIR1, self::TEST_DIR2);
		$this->assertTrue(\is_dir($cwd.self::TEST_DIR1.self::TEST_DIR2));
		// create test file
		$filepath = $cwd.self::TEST_DIR1.self::TEST_DIR2.self::TEST_FILE;
		$this->assertTrue(\touch($filepath));
		$this->assertTrue(\is_file($filepath));
		// delete test dirs
		System::rmDir($cwd, self::TEST_DIR1);
		$this->assertFalse(\is_dir($cwd.self::TEST_DIR1));
	}
	/**
	 * @covers ::mkDir
	 */
	public function test_mkDir_Exception() {
		$cwd = $this->getCWD();
		try {
			// fail to create multiple directories
			System::mkDir($cwd, self::TEST_DIR1.self::TEST_DIR2);
		} catch (\Exception $e) {
			$this->assertEquals(
					\sprintf(
							'dir argument contains illegal characters! %s != %s',
							Strings::Trim(self::TEST_DIR1.self::TEST_DIR2, '/'),
							\str_replace('/', '', self::TEST_DIR1.self::TEST_DIR2)
					),
					$e->getMessage()
			);
			return;
		}
		$this->assertTrue(FALSE, 'Failed to throw expected exception!');
	}



	private function getCWD() {
		$cwd = Strings::ForceEndsWith(\getcwd(), '/');
		$this->assertFalse(empty($cwd));
		$this->assertFalse($cwd == '/');
		return $cwd;
	}



}
