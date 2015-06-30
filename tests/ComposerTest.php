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

use pxn\phpUtils\Composer;
use pxn\phpUtils\Strings;

class ComposerTest extends \PHPUnit_Framework_TestCase {



	public function testGetVersion() {
		$composer = new Composer(__DIR__.'/../');
		$this->assertNotNull($composer);
		$version = $composer->getVersion();
		$this->assertNotEmpty($version);
		$this->assertTrue(\strpos($version, '.') !== FALSE);
	}
	public function testGetHomepage() {
		$composer = new Composer(__DIR__.'/../');
		$this->assertNotNull($composer);
		$homepage = $composer->getHomepage();
		$this->assertNotEmpty($homepage);
		$this->assertTrue(Strings::StartsWith($homepage, 'http'));
		$this->assertTrue(\strpos($homepage, '.') !== FALSE);
	}



}
