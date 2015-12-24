<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class System {
	private function __construct() {}



	###########
	## Shell ##
	###########



	public static function exec($command) {
		$command = \trim($command);
		if(empty($command)) return FALSE;
		$log = self::log();
		// run the command
		\exec($command, $output, $return);
		// command failed
		if($return !== 0) {
			$log->warning(\sprintf('Command failed: %s', $command));
		}
		// log output
		if(!empty($output) && \is_array($output)) {
			foreach($output as $line) {
				if(empty($line)) continue;
				$log->info($line);
			}
		}
		return ($return === 0);
	}



	#################
	## File System ##
	#################



	public static function mkDir($dir, $mode=644) {
		if(empty($dir)) throw new \Exception('dir argument is required');
		if(!\is_int($mode)) throw new \Exception('mode argument must be an integer!');
		$oct = \octdec($mode);
		$log = self::log();
		// prepend cwd
		if(!Strings::StartsWith($dir, '/'))
			$dir = Strings::BuildPath(\getcwd(), $dir);
		// dir already exists
		if(\is_dir($dir)) {
			$log->debug(\sprintf('Found existing directory: %s', $dir));
			return;
		}
		// build paths array
		$path = '/';
		$array = \explode('/', $dir);
		$nodes = [];
		$index = 0;
		foreach($array as $part) {
			if(empty($part)) continue;
			$path .= San::SafeDir($part);
			$nodes[$index++] = $path;
		}
		unset($path, $array);
		$count = \count($nodes);
		// find first not existing
		for($start = 0; $start < $count; $start++) {
			if(!\is_dir($nodes[$start])) break;
		}
		// all exist
		if($start == $count) return;
		// create directories
		for($index = $start; $index < $count; $index++) {
			$path = $nodes[$index];
			$log->debug(\sprintf('Creating: %s', $path));
			\mkdir($path, $oct);
		}
//		\clearstatcache(TRUE, $dir);
		// ensure created directories exist
		if(!\is_dir($dir)) throw new \Exception(\sprintf('Failed to create directory: %s', $dir));
	}
	public static function rmDir($dir) {
		if(empty($dir)) throw new \Exception('dir argument is required');
		$log = self::log();
		// ensure exists
		$temp = \realpath($dir);
		if(empty($temp)) throw new \Exception(\sprintf('dir not found, cannot delete! %s', $dir));
		$dir = $temp;
		unset($temp);
		\clearstatcache(TRUE, $dir);
		if(!\is_dir($dir)) throw new \Exception(\sprintf('dir argument is not a directory! %s', $dir));
		if($dir == '/')    throw new \Exception('cannot delete / directory!');
		// list contents
		$array = \scandir($dir);
		if($array == FALSE) throw new \Exception(\sprintf('Failed to list contents of directory: %s', $dir));
		foreach($array as $entry) {
			if($entry == '.' || $entry == '..')
				continue;
			$full = Strings::BuildPath($dir, $entry);
			if(\is_dir($full)) {
				self::rmDir($full);
			} else {
				$log->debug(\sprintf('Removing file: %s', $entry));
				\unlink($full);
			}
//			\rmdir($full);
//			$log->debug(\sprintf('Removing directory: %s', $entry));
		}
		\rmdir($dir);
//		\clearstatcache(TRUE, $dir);
		if(\is_dir($dir)) throw new \Exception(\sprintf('Failed to remove directory: %s', $dir));
		$log->debug(\sprintf('Removing directory: %s', $dir));
	}



	public static function log() {
		return Logger::get('SHELL');
	}



}
