<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;

use pxn\phpUtils\Config;
use pxn\phpUtils\Strings;


abstract class Website {

	private static $instance = NULL;

	private $render = NULL;
	private $hasRendered = FALSE;

	private $pageName     = NULL;
	private $pageDefault = 'home';



	public static function get() {
//		if(self::$instance == NULL)
//			self::$instance = new self();
		return self::$instance;
	}
	public static function peak() {
		return self::$instance;
	}
	public function __construct() {
		if (self::$instance != NULL) {
			fail('Website instance already started!');
			exit(1);
		}
		self::$instance = $this;
		// get arguments from url
		if (isset($_SERVER['REQUEST_URI'])) {
			$str = $_SERVER['REQUEST_URI'];
			$str = Strings::Trim($str, '/');
			$args = \explode('/', $str);
			if (isset($args[0])) {
				$this->page = (
						empty($args[0])
						? NULL
						: $args[0]
				);
				unset($args[0]);
			}
			// extra args
			$this->args = $args;
		}
		// init render handler
		$this->getRender();
		// render at shutdown
		\register_shutdown_function([$this, 'shutdown']);
	}



	public function setIcon($iconfile) {
		Config::set('icon', $iconfile);
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
		$this->setRendered();
		$render = $this->getRender();
		//echo $render->doRender();
		$render->doRender();
	}
	public function shutdown() {
		if ($this->hasRendered()) {
			return;
		}
		$this->doRender();
	}



	public function hasRendered() {
		return $this->hasRendered;
	}
	public function setRendered($value=NULL) {
		if ($value === NULL) {
			$value = TRUE;
		}
		$this->hasRendered = ($value == TRUE);
	}



	public function getTwig() {
		$render = $this->getRender();
		return $render->getTwig();
	}
	public function getTpl($filename) {
		$render = $this->getRender();
		return $render->getTpl($filename);
	}



	public function getPageName() {
		if ($this->pageName != NULL) {
			return $this->pageName;
		}
		return $this->pageDefault;
	}
	public function getPageContents($page=NULL) {
		if ($page != NULL) {
			$this->page = $page;
		}
		$page = $this->getPage();
		if (empty($page)) {
			fail('Page value could not be found!');
			exit(1);
		}
		// website page class
		$clss = "\\pxn\\gcWebsite\\pages\\page_{$page}";
		if (\class_exists($clss, TRUE)) {
			$obj = new $clss();
			return $obj->getPageContents();
		}
		// return 404 page
		if ($page != '404') {
			\http_response_code(404);
			return $this->getPageContents('404');
		}
		// 404 page not found
		return '<h1>404 - Page Not Found!</h1>';
	}



}
