<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class ComposerTools {

	private static $instances = [];

	protected $path;
	protected $json;



	public static function findJson($depth=2) {
		for ($i=0; $i<=$depth; $i++) {
			$path = \str_repeat('/..', $i);
			$path = \realpath( '.'.$path.'/' ).'/composer.json';
			// found file
			if (\file_exists($path)) {
				return self::get($path);
				break;
			}
		}
		return NULL;
	}
	public static function get($path=NULL) {
		$path = self::SanPath($path);
		if (isset(self::$instances[$path])) {
			$instance = self::$instances[$path];
		} else {
			$instance = new static($path);
			self::$instances[$path] = $instance;
		}
		return $instance;
	}
	protected function __construct($filePath=NULL) {
		if (empty($filePath) || !\is_file($filePath))
			throw new \Exception('Invalid composer.json file: '.$filePath);
		// read file contents
		$data = \file_get_contents($filePath);
		if ($data === FALSE)
			throw new \Exception('Failed to load composer.json '.$filePath);
		$this->json = \json_decode($data);
		unset($data);
		if (!isset($this->json->version))
			throw new \Exception('Failed to parse composer.json');
		$this->path = $filePath;
	}



	public static function SanPath($path) {
		// trim filename from end
		if (Strings::EndsWith($path, 'composer.json', FALSE))
			$path = \dirname($path);
		// normalize path
		$path = \realpath($path);
		// trim /src from end of path
		if (Strings::EndsWith($path, '/src', FALSE))
			$path = \realpath($path.'/../');
		// validate path
		if (empty($path) || $path == '/')
			throw new \Exception('Invalid path');
		// append filename
		return $path.'/composer.json';
	}
	public function getFilePath() {
		return $this->path;
	}



	public function getName() {
		if (!isset($this->json->name))
			return NULL;
		return $this->json->name;
	}
	public function getVersion() {
		if (!isset($this->json->version))
			return NULL;
		return $this->json->version;
	}
	public function getHomepage() {
		if (!isset($this->json->homepage))
			return NULL;
		return $this->json->homepage;
	}



}
