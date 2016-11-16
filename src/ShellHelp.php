<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class ShellHelp {

	protected $command  = NULL;
	protected $argName  = NULL;
	protected $optional = NULL;
	protected $many     = NULL;

	protected $args  = [];
	protected $flags = [];



	public function __construct($command=NULL, $argName=NULL, $optional=NULL, $many=NULL) {
		$this->command  = $command;
		$this->argName  = $argName;
		$this->optional = $optional;
		$this->many     = $many;
	}



	public function addArgument($name, $desc=NULL) {
		if (empty($name)) return;
//		$array = [];
//		if (!empty($desc)) {
//			$array['desc'] = $desc;
//		}
//		if ($optional === TRUE || $optional === FALSE) {
//			$array['optional'] = ($optional != FALSE);
//		}
//		if ($many === TRUE || $many === FALSE) {
//			$array['many'] = ($many != FALSE);
//		}
//		$this->args[$name] = $array;
		$this->args[$name] = $desc;
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
		echo "\n";

		// usage
		{
			$usage = [];
			if (!empty($this->command)) {
				$usage[] = (string) $this->command;
			}
			$usage[] = '[flags]';
			if (!empty($this->argName)) {
				$usage[] = '[--]';
				$name = $this->argName;
				$many = ($this->many != FALSE ? '...' : '');
				if ($this->optional) {
					$usage[] = "[<$name>]$many";
				} else {
					$usage[] = "<$name>$many";
				}
			}
			echo ShellTools::FormatString(
				"{color=orange}Usage:{reset}\n"
			);
			echo '  '.\implode($usage, ' ')."\n";
			echo "\n";
		}

		// prep and find longest line
		$maxSize = 0;
		{
			foreach ($this->args as $name => $desc) {
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
			$maxSize = Numbers::MinMax($maxSize, 20, 35) + 2;
		}

		// handle multi-line descriptions
		$funcWrapDesc = function($desc) use ($maxSize) {
			// multi-line string to array
			if (!\is_array($desc)) {
				$desc = (string) $desc;
				if (\mb_strpos($desc, "\n") !== FALSE) {
					$desc = \explode("\n", $desc);
				}
			}
			// single line description
			if (!\is_array($desc)) {
				return (string) $desc;
			}
			// multi line description
			$output = [];
			$first = TRUE;
			foreach ($desc as $line) {
				$line = Strings::Trim( (string) $line );
				if ($first) {
					$first = FALSE;
					$output[] = $line;
				} else {
					$padding = \str_repeat(' ', $maxSize + 4);
					$output[] = "{$padding}$line";
				}
			}
			return \implode("\n", $output);
		};

		// arguments
		{
			$name = \ucwords($this->argName);
			echo ConsoleShell::FormatString(
				"{color=orange}$name:{reset}\n"
			);
			foreach ($this->args as $name => $desc) {
				$size = 0;
				if (\is_string($name)) {
					$size = \mb_strlen($name);
				} else {
					$name = '';
				}
				$descLines = $funcWrapDesc($desc);
				$padding = \str_repeat(
					' ',
					Numbers::MinMax($maxSize - $size, 1)
				);
				echo ConsoleShell::FormatString(
					"  {color=green}$name{reset}$padding{$descLines}\n"
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
				$line = (isset($array['line']) ? (string) $array['line'] : '');
				$size = \mb_strlen($line);
				$descLines = $funcWrapDesc($desc);
				$padding = \str_repeat(
					' ',
					Numbers::MinMax($maxSize - $size, 1)
				);
				echo ConsoleShell::FormatString(
					"  {color=green}$line{reset}$padding{$descLines}\n"
				);
			}
			echo ConsoleShell::FormatString("{reset}\n");
		}

	}



}
