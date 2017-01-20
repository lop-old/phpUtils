<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use pxn\phpUtils\Defines;


class GitTools {

	private static $instances = [];

	protected $path = NULL;

	protected $git_tag_info = NULL;



	public static function get($path=NULL) {
		if (empty($path)) {
			$path = Paths::pwd();
		}
		$p = \realpath($path);
		if (empty($p)) {
			fail("Invalid path: $path",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// existing instance
		if (isset(self::$instances[$path]) && self::$instances[$path] != NULL) {
			return self::$instances[$path];
		}
		// new instance
		$instance = new static($p);
		self::$instances[$path] = $instance;
		return $instance;
	}
	protected function __construct($path) {
		$this->path = $path;
	}



	public function getTagInfo() {
		if ($this->git_tag_info != NULL
		&& \is_array($this->git_tag_info)
		&& \count($this->git_tag_info) > 0) {
			return $this->tag_info;
		}
		// get tag info from git
		$cmd = "/usr/bin/git describe --tags";
		$result = \shell_exec($cmd);
		// parse result
		$tag          = Strings::grabPart($result, '-');
		$commit_count = Strings::grabPart($result, '-g');
		$commit       = $result;
		$this->tag_info = [
			'tag'     => $tag,
			'current' => empty($commit),
			'count'   => (empty($commit_count) ? NULL : $commit_count ),
			'commit'  => (empty($commit)       ? NULL : $commit       ),
		];
		return $this->tag_info;
	}



}
