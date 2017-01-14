<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb\commands;

use pxn\phpUtils\Strings;

use pxn\phpUtils\pxdb\dbPool;


class dbCommand_List extends \pxn\phpUtils\pxdb\dbCommands {



	public function execute($pool, $table) {
		$poolName = dbPool::castPoolName($pool);
		$tableExists = $pool->hasTable($table);
		// found table
		if ($tableExists) {
			$msg = "Found:   {$poolName}:{$table}";
			$msg = Strings::PadLeft($msg, 30, ' ');
			$fields = $pool->getTableFields($table);
			$count = count($fields);
			$msg .= "[$count]";
			// list the fields
			if ($count > 0) {
				$msg .= ' ';
				$index = 0;
				foreach ($fields as $fieldName => $field) {
					if ($index++ > 0) {
						$msg .= ', ';
					}
					$fieldType = $field['type'];
					$msg .= "{$fieldType}|{$fieldName}";
				}
			}
			echo "$msg\n";
		// missing table
		} else {
			echo "Missing: {$poolName}:{$table}\n";
		}
		return TRUE;
	}



}
