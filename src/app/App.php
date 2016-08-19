<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\app;

use pxn\phpUtils\Strings;


abstract class App {

	private static $apps = [];
	private static $instance = NULL;
	private static $inited = FALSE;

	private $active = NULL;
	private $name = NULL;
	private $classpath = NULL;

	private $renderer = NULL;
	protected static $hasRendered = NULL;



	public static function get() {
		// pick an app by weight
		if (self::$instance == NULL) {
			self::$hasRendered = FALSE;
			$selected = NULL;
			$maxWeight = 0;
			foreach (self::$apps as $app) {
				$thisWeight = $app->getWeight();
				if ($thisWeight > $maxWeight) {
					$selected = $app;
					$maxWeight = $thisWeight;
				}
			}
			if ($selected == NULL) {
				$appInfo = [];
				foreach (self::$apps as $app) {
					$appInfo[$app->getName()] = 'Weight='.$app->getWeight();
				}
				fail('Failed to select an app!', $appInfo); ExitNow(1);
			}
			$selected->setActive();
		}
		return self::$instance;
	}
	public static function peak() {
		return self::$instance;
	}



	public static function init() {
		// init framework
		if (!self::$inited) {
			self::$inited = TRUE;
			// register shutdown hook
			\register_shutdown_function([
				__CLASS__,
				'Shutdown'
			]);
		}
		// init this app
		$clss = \get_called_class();
		$app = new $clss();
		return TRUE;
	}
	protected function __construct() {
		if (self::$instance != NULL) {
			$name = self::$instance->getName();
			fail('App class already loaded: {$name}'); ExitNow(1);
		}
		// app name and classpath
		{
			$path = Strings::Trim(
				\get_called_class(),
				'\\'
			);
			$pos = \mb_strrpos($path, '\\');
			if ($pos === FALSE) {
				$this->name = $path;
				$this->classpath = '';
			} else {
				$this->name = Strings::Trim(
					\mb_substr($path, $pos),
					'\\'
				);
				$this->classpath = Strings::Trim(
					\mb_substr($path, 0, $pos),
					'\\'
				);
			}
		}
		$name = $this->getName();
		if (isset(self::$apps[$name])) {
			fail("App already registered with the name: {$name}"); ExitNow(1);
		}
		self::$apps[$name] = $this;
	}
	protected abstract function initArgs();



	public static function Shutdown() {
		if (self::$hasRendered == TRUE) {
			return;
		}
		if (self::$instance == NULL) {
			self::get();
		}
		if (self::$instance == NULL) {
			fail('Failed to get an app instance!'); ExitNow(1);
		}
		self::$instance->doShutdown();
		self::$hasRendered = TRUE;
	}
	protected function doShutdown() {
//TODO:

echo "\n\n\n";
dump(self::$apps);
echo "\n\n\n";
foreach (self::$apps as $app) {
echo "<p>".$app->getName()." = ".($app->isActive() ? 'YES' : 'no')."</p>\n";
}
echo "\n\n\n";

echo "\n\n\n";
echo "<p>DO SHUTDOWN</p>";
echo "\n\n\n";
	}



	public function isActive() {
		if ($this->active === NULL) {
			return FALSE;
		}
		return ($this->active !== FALSE);
	}
	protected function setActive() {
		if (self::$instance != NULL) {
			$activeName = self::$instance->getName();
			fail("Another app instance is already active: {$activeName}"); ExitNow(1);
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
	protected function setDisactive() {
		if ($this->active !== NULL) {
			$appName = $this->getName();
			fail( $this->active !== FALSE
				? "App already active: {$appName}"
				: "App already disactive: {$appName}"
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



	public function hasRendered() {
		if ($this->hasRendered === NULL) {
			return FALSE;
		}
		return ($this->hasRendered !== FALSE);
	}
	public function setRendered($value=NULL) {
		if ($value === NULL) {
			$this->hasRendered = TRUE;
		} else {
			$this->hasRendered = ($value !== FALSE);
		}
	}



}
