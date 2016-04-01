<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\xLogger\handlers;


class ShellHandler implements \pxn\phpUtils\xLogger\Handler {

	protected $streamOut;
	protected $streamErr;



	public function __construct() {
		$this->streamOut = \fopen(
				'php://stdout',
				'w'
		);
		$this->streamErr = \fopen(
				'php://stderr',
				'w'
		);
	}



	public function write($msg) {


//TODO:
$handle = $this->streamOut;


		if (\is_array($msg)) {
			foreach ($msg as $m) {
				$this->write($m);
			}
			return;
		}
		\fwrite(
			$handle,
			"{$msg}\n"
		);
	}



}
