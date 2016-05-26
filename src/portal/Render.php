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
		$filename = Strings::ForceEndsWith(
			$filename,
			Defines::TEMPLATE_EXTENSION
		);
		// exact path
		if (\file_exists($filename)) {
			$fileinfo = \pathinfo($filename);
			$twig = $this->getTwig($fileinfo['dirname']);
			$tpl = $twig->loadTemplate($fileinfo['basename']);
			return $tpl;
		}
		// website src/html
		{
			$path = Strings::BuildPath(
				Paths::src(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = $this->getTwig($path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
		}
		// phpUtils src/html
		{
			$path = Strings::BuildPath(
				Paths::utils(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = $this->getTwig($path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
		}
		fail("Template file not found: {$filename}");
		return NULL;
	}



}
