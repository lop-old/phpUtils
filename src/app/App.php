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
	const DEFAULT_RENDER_MODE = 'main';

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
			$selected->setActive();
			self::$instance = $selected;
			self::$hasRendered = FALSE;
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
		return $app;
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
			fail ('Failed to get render mode!'); ExitNow(1);
			$appName = $this->getName();
			$renderMode = $this->getRenderMode();
			if (empty($renderMode)) {
				$renderMode = "<null>";
			}
			fail("Failed to get a render object for app/mode: $appName / $renderMode"); ExitNow(1);
		}
		// render page contents
		$render->doRender();
		$this->setRendered();
		return TRUE;
	}



	public function registerRender(Render $render) {
		$name = $render->getName();
		// multiple possible renderers
		if (isset($this->renders[$name])) {
			$this->renders[$name] = [
				$this->renders[$name],
				$render
			];
		} else {
			// single renderer
			$this->renders[$name] = $render;
		}
	}
	public function getRender($name=NULL) {
		// render already set
		if ($this->render != NULL) {
			return $this->render;
		}
		// get from config
		if (empty($name)) {
			$name = Config::get(self::KEY_RENDER_MODE);
		}
		// default render mode
		if (empty($name)) {
			$name = self::DEFAULT_RENDER_MODE;
		}
		if (empty($name) || !isset($this->renders[$name])) {
			fail("Unknown render mode: $name"); ExitNow(1);
		}
		$render = $this->renders[$name];
		// pick from multiple renderers
		if (\is_array($render)) {
			$array = $render;
			$highest = Defines::INT_MIN;
			foreach ($array as $r) {
				$wieght = $r->getWeight();
				if ($weight > $highest) {
					$highest = $weight;
					$render = $r;
				}
			}
		}
		$this->render = $render;
		return $this->render;
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
	protected function setActive() {
		if (self::$instance != NULL) {
			$name = self::$instance->getName();
			fail("Another app instance is already active: $name"); ExitNow(1);
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
			if ($app !== $this) {
				$app->setDisactive();
			}
		}
		$this->active = TRUE;
	}
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



	public function getName() {
		return $this->name;
	}
	public function getClasspath() {
		return $this->classpath;
	}



}
