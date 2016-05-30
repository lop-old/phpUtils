<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger\handlers;


class FileHandler implements \pxn\phpUtils\xLogger\Handler {

	protected $outputFile;
	protected $handle;



	public function __construct($outputFile) {
		$this->outputFile = $outputFile;
		$this->handle = \fopen(
				$this->outputFile,
				'a'
		);
		if ($this->handle == FALSE) {
			throw new \Exception("Failed to open log file for writing! {$outputFile}");
		}
	}
	public function __destruct() {
		\fclose($this->handle);
		$this->handle = NULL;
	}



	public function write($msg) {
		if (\is_array($msg)) {
			foreach ($msg as $m) {
				$this->write($m);
			}
			return;
		}
		\fwrite(
			$this->handle,
			"{$msg}\n"
		);
	}



}
