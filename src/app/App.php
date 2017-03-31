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
use pxn\phpUtils\Paths;
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

	private   $initedApp = FALSE;
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
				fail('Failed to select an app!',
					Defines::EXIT_CODE_INVALID_COMMAND);
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
			fail("An app is already registered: $clss  Existing: $existingClss",
				Defines::EXIT_CODE_INTERNAL_ERROR);
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
		{
			$log = xLog::getRoot();
			$log->setLevel(xLevel::ALL);
			$formatter = new FullFormat();
//			$formatter = new BasicFormat();
//			$formatter->setPrefix(' <<xBuild>> ');
			$log->setFormatter($formatter);
			$handler = new ShellHandler();
			$log->setHandler(
				$handler
			);
//			xLog::CaptureBuffer();
		}

		// load db configs
		{
			$paths = [
				Paths::entry(),
				Paths::base()
			];
			foreach ($paths as $path) {
				// find .htdb files
				$array = \scandir($path);
				\sort($array, \SORT_NATURAL);
				foreach ($array as $entry) {
					if ($entry == '.' || $entry == '..') {
						continue;
					}
					if (!Strings::StartsWith($entry, '.htdb')) {
						continue;
					}
					$file = Strings::BuildPath($path, $entry);
//TODO: log this
					require($file);
				}
			}
		}

		// register shutdown hook
		\register_shutdown_function([
			__CLASS__,
			'Shutdown'
		]);
		return TRUE;
	}
	public function terminating() {
	}
	protected function __construct() {
		if (self::$instance != NULL) {
			$name = self::$instance->getName();
			fail("App class already loaded: $name",
				Defines::EXIT_CODE_INTERNAL_ERROR);
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
	protected function doInitApp() {
		if ($this->initedApp) {
			return;
		}
		$this->initedApp = TRUE;
		$this->initApp();
	}
	protected function initApp() {
	}



	// shutdown hook
	public static function Shutdown() {
		if (self::$hasRendered === TRUE) {
			return;
		}
		$instance = self::get();
		if ($instance == NULL) {
			fail('Failed to get an app instance!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$result = $instance->doShutdown();
		// render failed
		if ($result === FALSE) {
			ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// render success or already done
		if ($result === TRUE || $result === NULL) {
			ExitNow(Defines::EXIT_CODE_OK);
		}
		// other render result
		$result = (int) $result;
		if ($result == 0) {
			ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		ExitNow($result);
	}
	protected function doShutdown() {
		if ($this->hasRendered()) {
			return NULL;
		}
		$result = $this->doRender();
		return $result;
	}
	protected function doRender() {
		if ($this->hasRendered()) {
			return NULL;
		}
		$render = $this->getRender();
		if ($render == NULL) {
			fail('Failed to get render mode!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
//			$appName = $this->getName();
//			$renderMode = $this->getRenderMode();
//			if (empty($renderMode)) {
//				$renderMode = "<null>";
//			}
//			fail("Failed to get a render object for app/mode: $appName / $renderMode",
//				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// render page contents
		$result = $render->doRender();
		$this->setRendered();
		return $result;
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
			fail("Unknown render mode: $name",
				Defines::EXIT_CODE_INTERNAL_ERROR);
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
			fail("Another app instance is already active: $name",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if ($this->active !== NULL) {
			if ($this->active === TRUE) {
				fail('This app instance is already active!',
					Defines::EXIT_CODE_INTERNAL_ERROR);
			}
			fail('Another app instance is already active!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// set app active states
		self::$instance = $this;
		foreach (self::$apps as $app) {
			if ($app !== $this) {
				$app->setDisactive();
			}
		}
		$this->active = TRUE;
		// init app
		$this->doInitApp();
	}
	protected function setDisactive() {
		if ($this->active !== NULL) {
			$appName = $this->getName();
			if ($this->active !== FALSE) {
				fail("App already active: $appName",
					Defines::EXIT_CODE_INTERNAL_ERROR);
			}
			fail("App already disactive: $appName",
				Defines::EXIT_CODE_INTERNAL_ERROR);
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
