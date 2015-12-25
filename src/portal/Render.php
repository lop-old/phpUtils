<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;


abstract class Render {



	public function __construct() {
	}



	public abstract function doRender();



	public function getTwig($path, $file) {
		$twigLoader = new \Twig_Loader_Filesystem(__DIR__);
				$twig = new \Twig_Environment(
			$twigLoader,
			array(
				'cache' => \pxn\phpUtils\Config::getTwigTempDir()
			)
		);
		$tpl = $twig->loadTemplate('test.htm');
		echo $tpl->render(
			array(
				'tag' => 'TAG'
			)
		);
	}




}
