<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal\pages;

use pxn\phpUtils\pxdb\dbPool;


abstract class blog extends \pxn\phpUtils\portal\Page {



	public function getPageContents() {
		$tpl = $this->getTpl(
			$this->getTplFilename()
		);
		$db = dbPool::get('main');
		$sql = 'SELECT `title`, `text` FROM `__table__blog_entries`';
		$db->Prepare($sql);
		$db->Execute();
		$blogEntries = [];
		while($db->Next()) {
			$blogEntries[] = [
				'title' => $db->getString('title'),
				'text'  => $db->getString('text')
			];
		}
		$db->release();
		return $tpl->render([
			'entries' => $blogEntries
		]);
	}



	protected abstract function getTplFilename();



}
