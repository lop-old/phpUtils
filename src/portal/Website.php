<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;


abstract class Website {

	private static $instance = NULL;

	private $render = NULL;



//	public static function get() {
//		if(self::$instance == NULL)
//			self::$instance = new self();
//		return self::$instance;
//	}
	public static function peak() {
		return self::$instance;
	}
	public function __construct() {
	}



	public function getRender() {
		if ($this->render == NULL) {
			$renderType = \pxn\phpUtils\Config::getRenderType();
			switch ($renderType) {
			case 'main':
				$this->render = new RenderMain();
				break;
			case 'splash':
				$this->render = new RenderSplash();
				break;
			case 'minimal':
				$this->render = new RenderMinimal();
				break;
			default:
				\fail ("Unknown render type: {$type}");
				exit(1);
			}
		}
		return $this->render;
	}
	public function doRender() {
		$render = $this->getRender();
		echo $render->doRender();
	}



}
