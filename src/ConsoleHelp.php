<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class ConsoleHelp {

	protected $command = NULL;
	protected $args  = [];
	protected $flags = [];



	public function __construct($command=NULL) {
		$this->command = $command;
	}



	public function addArgument($name, $desc=NULL, $optional=TRUE, $many=FALSE) {
		if (empty($name)) return;
		$array = [];
		if (!empty($desc)) {
			$array['desc'] = $desc;
		}
		if ($optional === TRUE || $optional === FALSE) {
			$array['optional'] = ($optional != FALSE);
		}
		if ($many === TRUE || $many === FALSE) {
			$array['many'] = ($many != FALSE);
		}
		$this->args[$name] = $array;
	}
	public function addFlag($flags, $desc=NULL) {
		$array = [];
		if (\is_array($flags)) {
			$array['flags'] = $flags;
		} else
		if (!empty($flags)) {
			$array['flags'] = [
				(string) $flags
			];
		}
		if (!empty($desc)) {
			$array['desc'] = $desc;
		}
		$this->flags[] = $array;
	}



	public function Display() {

		// usage
		{
			$usage = [];
			if (!empty($this->command)) {
				$usage[] = (string) $this->command;
			}
			$usage[] = '[flags]';
			$first = TRUE;
			foreach ($this->args as $name => $array) {
				$optional = (isset($array['optional']) ? $array['optional'] : FALSE);
				$many     = (isset($array['many'])     ? $array['many']     : FALSE);
				if ($first) {
					$first = FALSE;
					$usage[] = '[--]';
				}
				$line = '';
				if ($optional) {
					$usage[] = "[<{$name}>]".($many ? '...' : '');
				} else {
					$usage[] = "<{$name}>".($many ? '...' : '');
				}
			}
			echo ConsoleShell::FormatString(
				"{color=orange}Usage:{reset}\n"
			);
			echo '  '.\implode($usage, ' ')."\n";
			echo "\n";
		}

		// prep and find longest line
		$maxSize = 0;
		{
			foreach ($this->args as $name => $array) {
				// find longest line
				$len = \mb_strlen($name);
				if ($len > $maxSize) {
					$maxSize = $len;
				}
			}
			foreach ($this->flags as &$array) {
				if (!isset($array['flags'])) continue;
				$line = \implode($array['flags'], ', ');
				$firstLine = \reset($array['flags']);
				if (Strings::StartsWith($firstLine, '--')) {
					$line = "    {$line}";
				}
				$array['line'] = $line;
				$len = \mb_strlen($line);
				if ($len > $maxSize) {
					$maxSize = $len;
				}
			}
			unset($array);
			$maxSize = Numbers::MinMax($maxSize, 20, 40);
		}

		// arguments
		{
			echo ConsoleShell::FormatString(
				"{color=orange}Arguments:{reset}\n"
			);
			foreach ($this->args as $name => $array) {
				$desc = (isset($array['desc']) ? $array['desc'] : '');
				$size = 0;
				if (\is_string($name)) {
					$size = \mb_strlen($name);
				} else {
					$name = '';
				}
				$padding = \str_repeat(' ', $maxSize - $size);
				echo ConsoleShell::FormatString(
					"  {color=green}$name{reset}$padding{$desc}\n"
				);
			}
			echo "\n";
		}

		// flags
		{
			echo ConsoleShell::FormatString(
				"{color=orange}Flags:{reset}\n"
			);
			foreach ($this->flags as $array) {
				$desc = (isset($array['desc']) ? $array['desc'] : '');
				$line = (isset($array['line']) ? $array['line'] : '');
				$size = \mb_strlen($line);
				$padding = \str_repeat(' ', $maxSize - $size);
				echo ConsoleShell::FormatString(
					"  {color=green}$line{reset}$padding{$desc}\n"
				);
			}
			echo "\n";
		}

		ExitNow(1);
	}



}
