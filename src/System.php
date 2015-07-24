<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
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
		$log = self::logShell();
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



	public static function mkDir($path=NULL, $dir, $mode=644) {
		if(!\is_int($mode)) throw new \Exception('mode argument must be an integer!');
		$path = San::SafePath($path);
		$dir  = San::SafeDir($dir);
		$full = Strings::BuildPath($path, $dir);
		$full = Strings::ForceEndsWith($full, '/');
		// already exists
		if(\is_dir($full)) {
//			self::logShell()
//			Logger::get('SYSTEM')->info(\sprintf(
//					'Directory already exists:  %s  %s',
//					$path,
//					$dir
//			));
			return FALSE;
		}
		// create directory
		self::exec(\sprintf(
				'cd "%s" && mkdir -pv %s',
				$path,
				$dir
		));
		return TRUE;
	}
	public static function rmDir($path=NULL, $dir) {
		$path = San::SafePath($path);
		$dir  = San::SafeDir($dir);
		$full = Strings::BuildPath($path, $dir);
		$full = Strings::ForceEndsWith($full, '/');
		// target not found
		if(!\is_dir($full)) {
			self::logShell()->debug(
					\sprintf(
							'Cannot rm, target directory not found: %s',
							$full
					)
			);
			return FALSE;
		}
		// list contents
		\clearstatcache(TRUE, $full);
		$array = \scandir($full);
		if($array == FALSE) {
			throw new \Exception(
					\sprintf(
							'Failed to list contents of directory: %s',
							$full
					)
			);
		}
		// not empty
		if(!empty($array)) {
			foreach($array as $entry) {
				if($entry == '.' || $entry == '..') continue;
				if(\is_dir($full.$entry)) {
					self::rmDir($full, $entry);
				} else {
					unlink($full.$entry);
					self::logShell()->info(
							\sprintf(
									'Removed file:  %s  %s',
									$full,
									$entry
							)
					);
				}
			}
		}
		// rm the dir
		\rmdir($full);
		\clearstatcache(TRUE, $full);
		// ensure delete was successful
		if(\is_dir($full)) {
			self::logShell()->critical(
					\sprintf(
							'Failed to remove directory: %s',
							$full
					)
			);
			exit(1);
		}
		self::logShell()->info(
				\sprintf(
						'Removed dir:  %s  %s',
						$path,
						$dir
				)
		);
		return TRUE;
//		// delete directory
//		$result = self::exec(\sprintf(
//				'cd "%s" && rm -Rvf --preserve-root "%s" 2>&1',
//				$path,
//				$dir
//		));
	}



	public static function logShell() {
		return Logger::get('SHELL');
	}



}
