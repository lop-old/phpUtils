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

use pxn\phpUtils\ComposerTools;
use pxn\phpUtils\Strings;

/**
 * @coversDefaultClass \pxn\phpUtils\ComposerTools
 */
class ComposerTest extends \PHPUnit_Framework_TestCase {



	/**
	 * @covers ::get
	 * @covers ::__construct
	 * @covers ::getPath
	 */
	public function testPaths() {
		$expect = \realpath(__DIR__.'/../composer.json');
		// default path
		$composer = ComposerTools::get();
		$this->assertNotNull($composer);
		$this->assertEquals($expect, $composer->getFilePath());
		unset($composer);
		// exact path
		$path = \realpath(__DIR__.'/../');
		$composer = ComposerTools::get($path.'/composer.json');
		$this->assertNotNull($composer);
		$this->assertEquals($expect, $composer->getFilePath());
		unset($composer, $path);
		// invalid path
		try {
			$composer = ComposerTools::get('notexisting');
			$this->assertFalse(TRUE, 'Expected exception not thrown!');
			return;
		} catch (\Exception $ignore) {}
	}



	public function testInstances() {
		$a = ComposerTools::get();
		$b = ComposerTools::get();
		$this->assertTrue($a === $b);
	}



	/**
	 * @covers ::get
	 * @covers ::__construct
	 * @covers ::getVersion
	 * @covers ::getHomepage
	 */
	public function testValues() {
		$composer = ComposerTools::get();
		$this->assertNotNull($composer);
		// version
		$version = $composer->getVersion();
		$this->assertNotEmpty($version);
		$this->assertTrue(\strpos($version, '.') !== FALSE);
		// homepage
		$homepage = $composer->getHomepage();
		$this->assertNotEmpty($homepage);
		$this->assertTrue(Strings::StartsWith($homepage, 'http'));
		$this->assertTrue(\strpos($homepage, '.') !== FALSE);
	}



}