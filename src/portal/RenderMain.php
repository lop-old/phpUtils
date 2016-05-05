<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;

use pxn\phpUtils\Config;
use pxn\phpUtils\Defines;


class RenderMain extends Render {



	public function __construct() {
		parent::__construct();
	}



	public function doRender() {
		// get page contents
		$pageContents = self::$website->getPageContents();
		// get page title
		$pageTitle = self::$website->getPageName();
		if ($pageTitle instanceof \pxn\phpUtils\portal\Page) {
			$pageTitle = $pageTitle->getTitle();
		} else {
			$pageTitle = (String) $pageTitle;
		}
		$title = \str_replace(
				'{pagetitle}',
				$pageTitle,
				Config::get(Defines::KEY_SITE_TITLE)
		);
		$iconFile  = Config::get(Defines::KEY_FAV_ICON);
		// load template file
		$tpl = self::$website->getTpl('main');
		// start rendering html
		$CRLF = Defines::CRLF;
		$TAB  = Defines::TAB;
		echo
			'<!DOCTYPE html>'.$CRLF.
			'<html lang="en">'.$CRLF.
			'<head>'.$CRLF.
			'<meta charset="utf-8" />'.$CRLF.
			'<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.$CRLF.
			'<meta name="viewport" content="width=device-width, initial-scale=1" />'.$CRLF.
			"<title>{$title}</title>".$CRLF.

			// fav icon
			(empty($iconFile) ? '' :
				'<link rel="shortcut icon" href="{$iconFile}" type="image/x-icon" />'.$CRLF.
				'<link rel="icon" href="{$iconFile}" type="image/x-icon" />'.$CRLF
			).

			'<link rel="stylesheet" href="static/main.css" />'.$CRLF.
			'<link rel="stylesheet" href="static/bootstrap/dist/css/bootstrap.min.css" />'.$CRLF.
			'<script src="static/jquery/jquery.min.js"></script>'.$CRLF.
			'<script src="static/bootstrap/dist/js/bootstrap.min.js"></script>'.$CRLF.

			'<meta http-equiv="refresh" content="2" />'.$CRLF.

			'</head>'.$CRLF.
			'<body>'.$CRLF;
		\ob_flush();
		// render with twig
		echo $tpl->render([
				'PageContents' => &$pageContents
		]);
		echo
			'</body>'.$CRLF.
			'</html>';
		\ob_flush();
	}



}
