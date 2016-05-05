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

	protected $tplPath = '';
	protected $twig = NULL;



	public function __construct() {
		if (self::$website == NULL) {
			self::$website = \pxn\phpUtils\portal\Website::get();
		}
		$this->tplPath = Paths::src().'/html';
	}



	public abstract function doRender();



	public function getTwig() {
		if ($this->twig != NULL) {
			return $this->twig;
		}
		$cachePath = Paths::getTwigCachePath();
		$twigLoader = new \Twig_Loader_Filesystem(
			$this->tplPath
		);
		$this->twig = new \Twig_Environment(
			$twigLoader,
			[
				'debug' => \pxn\phpUtils\debug(),
				'cache' => $cachePath
			]
		);
		return $this->twig;
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
