<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

class ComposerTools {

	protected $json;



	public function __construct($path=NULL) {
		if(empty($path))
			$path = __DIR__.'/../';
		if(!Strings::EndsWith($path, 'composer.json')) {
			if(!Strings::EndsWith($path, 'composer.json'))
				$path .= '/';
			$path .= 'composer.json';
		}
		$data = \file_get_contents($path);
		if($data === FALSE)
			throw new \Exception('Failed to load composer.json '.$path);
		$this->json = \json_decode($data);
		unset($data);
		if(!isset($this->json->version))
			throw new \Exception('Failed to load composer.json');
	}



	public function getVersion() {
		if(!isset($this->json->version))
			return NULL;
		return $this->json->version;
	}
	public function getHomepage() {
		if(!isset($this->json->homepage))
			return NULL;
		return $this->json->homepage;
	}



}
