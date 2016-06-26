<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\console;

use pxn\phpUtils\San;
use pxn\phpUtils\Numbers;


class Dialog_Menu extends Dialog {

	protected $msg   = NULL;
	protected $title = NULL;
	protected $options = [];



	public function run() {
		
		$cmd = $this->getCommand();
		$this->setCmd(
			$cmd
		);
		echo "CMD: ";
		dump($cmd);
		parent::run($cmd);
		return $this->result;
	}



	public function getCommand() {
		if (\count($this->options) == 0) {
			fail('Menu Dialog requires options!');
			exit(1);
		}
		$msg = \escapeshellarg($this->msg);
		$menuHeight = Numbers::MinMax(
			\count($this->options),
			3,
			100
		);
		$height = Numbers::MinMax(
			$menuHeight + 7,
			11,
			107
		);
		$width = 20;
		// build command
		$cmd = [];
		$cmd[] = 'dialog';
		if (!empty($this->title)) {
			$title = \escapeshellarg($this->title);
			$cmd[] = '--title';
			$cmd[] = $title;
		}
		$cmd[] = '--menu';
		$cmd[] = $msg;
		$cmd[] = $height;
		$cmd[] = $width;
		$cmd[] = $menuHeight;
		foreach ($this->options as $key => $val) {
			$sizeKey = \mb_strlen($key);
			$sizeVal = \mb_strlen($val);
			if ($sizeKey + $sizeVal > $width) {
				$width = $sizeKey + $sizeVal;
			}
			$cmd[] = $key;
			$cmd[] = $val;
		}
		return \implode($cmd, ' ');
	}



	public function setMsg($msg) {
		$this->msg = $msg;
		return $this;
	}
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	public function addOption($key, $val) {
		$key = San::AlphaNum(   (string) $key );
		$val = \escapeshellarg( (string) $val );
		if (empty($key)) {
			$this->options[] = $val;
		} else {
			$this->options[$key] = $val;
		}
		return $this;
	}



}
