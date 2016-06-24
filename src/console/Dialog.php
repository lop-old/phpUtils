<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\console;


abstract class Dialog {

	protected $cmd    = NULL;
	protected $result = NULL;



	public abstract function getCommand();



	public function run() {
		$cmd = (string) $this->cmd;
		if (empty($cmd)) {
			fail('cmd argument is required!');
			exit(1);
		}
		$pipes = [NULL, NULL, NULL];
		$in  = fopen ('php://stdin',  'r');
		$out = fopen ('php://stdout', 'w');
		$streams = [
			0 => $in,
			1 => $out,
			2 => ['pipe', 'w']
		];
		$p = \proc_open(
			$cmd,
			$streams,
			$pipes
		);
		$this->result = \stream_get_contents($pipes[2]);
		\fclose($pipes[2]);
		\fclose($out);
		\fclose($in);
		\proc_close($p);
		return $this->result;
	}



	public function setCmd($cmd) {
		$this->cmd = $cmd;
	}
	public function getResult() {
		return $this->result;
	}



}
