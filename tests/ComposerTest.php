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
	 * @covers ::getVersion
	 * @covers ::getHomepage
	 */
	public function testValues() {
		$composer = ComposerTools::get(__DIR__.'/../');
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
