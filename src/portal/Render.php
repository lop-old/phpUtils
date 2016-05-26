<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;

use pxn\phpUtils\Strings;
use pxn\phpUtils\Paths;
use pxn\phpUtils\Defines;


abstract class Render {

	protected static $website = NULL;

	protected $twigs = array();



	public function __construct() {
		if (self::$website == NULL) {
			self::$website = \pxn\phpUtils\portal\Website::get();
		}
	}



	public abstract function doRender();



	public function getTwig($path) {
		if (!\is_dir($path)) {
			fail("Template path doesn't exist: {$path}");
			exit(1);
		}
		// existing twig instance
		if (isset($this->twigs[$path]) && $this->twigs[$path] != NULL) {
			return $this->twigs[$path];
		}
		// new twig instance
		$twigLoader = new \Twig_Loader_Filesystem(
			$path
		);
		$twig = new \Twig_Environment(
			$twigLoader,
			[
				'debug' => \pxn\phpUtils\debug(),
				'cache' => Paths::getTwigCachePath()
			]
		);
		$this->twigs[$path] = $twig;
		return $twig;
	}
	public function getTpl($filename) {
		$tplFile = Strings::ForceEndsWith(
			$filename,
			Defines::TEMPLATE_EXTENSION
		);
		$twig = $this->getTwig();
		$tpl = $twig->loadTemplate($tplFile);
		return $tpl;
	}



}
