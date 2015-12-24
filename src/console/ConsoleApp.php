<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\console;

use pxn\phpUtils\ComposerTools;


class ConsoleApp extends \Symfony\Component\Console\Application {

	protected $router   = NULL;
	protected $composer = NULL;



	public function __construct($name=NULL, $version=NULL) {
		if(empty($name)) {
			$name = $this->getComposer()
					->getName();
		}
		if(empty($version)) {
			$version = $this->getComposer()
					->getVersion();
		}
		parent::__construct($name, $version);
	}



	public function getComposer() {
		if($this->composer == NULL)
			$this->composer = ComposerTools::find(2);
		if($this->composer == NULL)
			throw new FileNotFoundException('composer.json file not found');
		return $this->composer;
	}



	public function getRouter() {
		return $this->router;
	}
	public function setRouter(Router $router) {
		$this->router = $router;
	}



}
