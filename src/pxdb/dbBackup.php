<?php
//https://github.com/ifsnop/mysqldump-php/blob/master/src/Ifsnop/Mysqldump/Mysqldump.php

/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;

use pxn\phpUtils\Strings;
use pxn\phpUtils\System;
use pxn\phpUtils\Defines;


final class dbBackup {
	private function __construct() {}


/*

	public static function export($pool, $tableName, $path) {
		if ($pool == NULL) {
			fail('pool argument is required!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (empty($tableName)) {
			fail('tableName argument is required!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (empty($path)) {
			fail('path argument is required!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$isShell = System::isShell();
		$db = $pool->getDB(dbConn::ERROR_MODE_EXCEPTION);
		if (!\is_dir($path)) {
			\mkdir($path, 0700, TRUE);
		}
		// generate backup data
		$array = [];
		$sql = '';
		$count = 0;
		try {
			if ($isShell) {
				echo "\n\n == Exporting blog_entries table..\n";
			}
			$sql = "SELECT `entry_id`, `title`, `body`, ".
				"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
				"FROM `__TABLE__blog_entries` ".
				"ORDER BY `timestamp` ASC, `entry_id` ASC ";
			$db->Execute($sql);
			while ($db->hasNext()) {
				$count++;
				$id = $db->getInt('entry_id');
				$title = $db->getString('title');
				$body = $db->getString('body');
				$timestamp = $db->getInt('timestamp');
				$json = \json_encode(
					[
						'id'        => $id,
						'title'     => $title,
						'timestamp' => $timestamp,
						'body'      => $body
					],
					\JSON_PRETTY_PRINT
				);
				if (empty($json)) {
					fail("Failed to generate json data for entry id: $id",
						Defines::EXIT_CODE_INTERNAL_ERROR);
				}
				// write backup file
				$filePath = Strings::BuildPath(
					$path,
					"{$id}.txt"
				);
				if (\file_exists($filePath)) {
					fail("Cannot export, file already exists: $filePath",
						Defines::EXIT_CODE_INTERNAL_ERROR);
				}
				$result = \file_put_contents(
					$filePath,
					$json,
					\LOCK_EX
				);
				if ($result === FALSE) {
					fail("Failed to write export file: $filePath",
						Defines::EXIT_CODE_IO_ERROR);
				}
			}
		} catch (\PDOException $e) {
			fail("Query failed: $sql",
				Defines::EXIT_CODE_INTERNAL_ERROR, $e);
		}
		if ($isShell) {
			echo "Exported [ $count ] blog entries.\n";
		}
		$db->release();
		return TRUE;
	}
	public static function import($pool, $filePath) {
		if ($pool == NULL) {
			fail('pool argument is required!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (!file_exists($filePath)) {
			fail("File not found: $filePath",
				Defines::EXIT_CODE_IO_ERROR);
		}
		$data = \file_get_contents($filePath);
		if ($data === FALSE) {
			fail("Failed to read file contents: $filePath",
				Defines::EXIT_CODE_IO_ERROR);
		}
		
		
		
		
		
		
	}

*/


}
