<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


class dbCommand_List extends dbCommands {



	public function execute($pool, $table) {
		$poolName = dbPool::castPoolName($pool);
		$tableExists = $pool->hasTable($table);
		if ($tableExists) {
			echo "Found:   {$poolName}:{$table}\n";
		} else {
			echo "Missing: {$poolName}:{$table}\n";
		}
		return TRUE;
	}



}
