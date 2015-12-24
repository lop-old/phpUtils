<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
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
		// ensure clean
		$this->assertFalse(
				\is_dir($cwd.self::TEST_DIR1),
				\sprintf(
						'Temporary test directory already exists: %s',
						self::TEST_DIR1
				)
		);
		// create test dirs
		System::mkDir($cwd.self::TEST_DIR1.self::TEST_DIR2, 700);
		$this->assertTrue(
				\is_dir($cwd.self::TEST_DIR1.self::TEST_DIR2),
				\sprintf(
						'Failed to create temporary test directory: %s',
						$cwd.self::TEST_DIR1.self::TEST_DIR2
				)
		);
		// create test file
		$this->assertTrue(\touch(
				$cwd.self::TEST_DIR1.self::TEST_DIR2.'TestFile.txt'
		));
		$this->assertTrue(\is_file(
				$cwd.self::TEST_DIR1.self::TEST_DIR2.'TestFile.txt'
		));
		// recursively delete test directories
		System::rmDir($cwd.self::TEST_DIR1);
		// ensure removed
		$this->assertFalse(
				\is_dir($cwd.self::TEST_DIR1),
				\sprintf(
						'Failed to remove temporary test directory: %s',
						self::TEST_DIR1
				)
		);
	}



	private function getCWD() {
		$cwd = Strings::ForceEndsWith(\getcwd(), '/');
		$this->assertFalse(empty($cwd));
		$this->assertFalse($cwd == '/');
		return $cwd;
	}



}
