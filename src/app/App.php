<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\app;

use pxn\phpUtils\Config;
use pxn\phpUtils\Strings;
use pxn\phpUtils\Defines;

use pxn\phpUtils\xLogger\xLog;
use pxn\phpUtils\xLogger\xLevel;
use pxn\phpUtils\xLogger\formatters\FullFormat;
use pxn\phpUtils\xLogger\handlers\ShellHandler;


abstract class App {

	const KEY_RENDER_MODE = 'Render Mode';

	private static $apps        = [];
	private static $instance    = NULL;
	private static $inited      = FALSE;
	private static $hasRendered = NULL;

	protected $active    = NULL;
	protected $name      = NULL;
	protected $classpath = NULL;

	protected $render  = NULL;
	protected $renders = [];



	public static function get() {
		// pick an app by weight
		if (self::$instance == NULL) {
			$selected = NULL;
			$maxWeight = Defines::INT_MIN;
			foreach (self::$apps as $app) {
				$weight = $app->getWeight();
				if ($weight > $maxWeight) {
					$selected = $app;
					$maxWeight = $weight;
				}
			}
			if ($selected == NULL) {
				fail('Failed to select an app!'); ExitNow(1);
			}
			self::$instance = $selected;
			self::$hasRendered = FALSE;
			$selected->setActive();
		}
		return self::$instance;
	}
	public static function peak() {
		return self::$instance;
	}



	// init/register this app
	public static function register() {
		// init the framework
		self::init();
		// register instance of this app
		$clss = \get_called_class();
		$app = new $clss();
		$name = $app->getName();
		// app name already exists
		if (isset(self::$apps[$name])) {
			$existingClss = self::$apps[$name]->getClasspath();
			fail("An app is already registered: $clss  Existing: $existingClss"); ExitNow(1);
		}
		self::$apps[$name] = $app;
		return TRUE;
	}
	// init framework once
	protected static function init() {
		if (self::$inited) {
			return FALSE;
		}
		self::$inited = TRUE;

		// init logger
		$log = xLog::getRoot();
		$log->setLevel(xLevel::ALL);
		$formatter = new FullFormat();
//		$formatter = new BasicFormat();
//		$formatter->setPrefix(' <<xBuild>> ');
		$log->setFormatter($formatter);
		$handler = new ShellHandler();
		$log->setHandler(
			$handler
		);
//		xLog::CaptureBuffer();

		// register shutdown hook
		\register_shutdown_function([
			__CLASS__,
			'Shutdown'
		]);
		return TRUE;
	}
	protected function __construct() {
		if (self::$instance != NULL) {
			$name = self::$instance->getName();
			fail("App class already loaded: $name"); ExitNow(1);
		}
		// app name and classpath
		{
			// old way
			//$reflect = new \ReflectionClass(self::get());
			//$clss = $reflect->getName();
			//unset($reflect);
			$tmp = \get_called_class();
			$path = Strings::Trim($tmp, '\\');
			unset($tmp);
			$pos = \mb_strrpos($path, '\\');
			if ($pos === FALSE || $pos <= 3) {
				$this->name = $path;
				$this->classpath = '';
			} else {
				$tmp = \mb_substr($path, $pos);
				$this->name = Strings::Trim($tmp, '\\');
				$tmp = \mb_substr($path, 0, $pos);
				$this->classpath = Strings::Trim($tmp, '\\');
				unset($tmp);
			}
		}
	}



	// shutdown hook
	public static function Shutdown() {
		if (self::$hasRendered === TRUE) {
			return;
		}
		$instance = self::get();
		if ($instance == NULL) {
			fail('Failed to get an app instance!'); ExitNow(1);
		}
		$instance->doShutdown();
	}
	protected function doShutdown() {
		if ($this->hasRendered()) {
			return;
		}
		$this->doRender();
	}
	protected function doRender() {
		if ($this->hasRendered()) {
			return FALSE;
		}
		$render = $this->getRender();
		if ($render == NULL) {
			$appName = $this->getName();
			$renderMode = $this->usingRenderMode();
			if (empty($renderMode)) {
				$renderMode = "''";
			}
			fail("Failed to get a render object for app/mode: $appName / $renderMode"); ExitNow(1);
		}
		// render page contents
		$render->doRender();
		$this->setRendered();
		return TRUE;
	}



	public function registerRender(\pxn\phpUtils\app\render\Render $render) {
		$name = $render->getName();
		if (isset($this->renders[$name])) {
			fail("A render has already been registered with the name: $name"); ExitNow(1);
		}
		$this->renders[$name] = $render;
	}



	public function getRenderMode() {
		// find by name in config
		$name = $this->peakRenderMode();
		if (!empty($name)) {
			return $name;
		}
		// find by weight
		$maxWeight = Defines::INT_MIN;
		$name = NULL;
		foreach ($this->renders as $r) {
			$weight = $r->getWeight();
			if ($weight > $maxWeight) {
				$maxWeight = $weight;
				$name = $r->getName();
			}
		}
		return $name;
	}
	public function peakRenderMode() {
		return Config::get(self::KEY_RENDER_MODE);
	}
	public function getRender() {
		if ($this->render != NULL) {
			return $this->render;
		}
		$name = $this->getRenderMode();
		if (isset($this->renders[$name])) {
			$this->render = $this->renders[$name];
			return $this->render;
		}
		return NULL;
	}



	public function hasRendered() {
		if (self::$hasRendered === NULL) {
			return FALSE;
		}
		if ($this->render === NULL) {
			return FALSE;
		}
		return (self::$hasRendered === TRUE);
	}
	public function setRendered($value=NULL) {
		if ($value === NULL) {
			self::$hasRendered = TRUE;
		} else {
			self::$hasRendered = ($value === TRUE);
		}
	}



	public function isActive() {
		if ($this->active === NULL) {
			return NULL;
		}
		return ($this->active === TRUE);
	}
/*
	protected function setActive() {
		if (self::$instance != NULL) {
			$activeName = self::$instance->getName();
			fail("Another app instance is already active: $activeName"); ExitNow(1);
		}
		if ($this->active !== NULL) {
			fail( $this->active === TRUE
				? 'This app instance is already active!'
				: 'Another app instance is already active!'
			);
			ExitNow(1);
		}
		// set app active states
		self::$instance = $this;
		foreach (self::$apps as $app) {
			if ($app === $this) continue;
			$app->setDisactive();
		}
		$this->active = TRUE;
	}
*/
/*
	protected function setDisactive() {
		if ($this->active !== NULL) {
			$appName = $this->getName();
			fail( $this->active !== FALSE
				? "App already active: $appName"
				: "App already disactive: $appName"
			);
			ExitNow(1);
		}
		$this->active = FALSE;
	}
*/



	public function getName() {
		return $this->name;
	}
/*
	public function getClasspath() {
		return $this->classpath;
	}
*/



}
