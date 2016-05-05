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
	}



	public function doRender() {
		$CRLF = Defines::CRLF;
		$TAB  = Defines::TAB;
		$iconfile = Config::get(Defines::FAV_ICON_KEY);
		$output = '';
		$output .=
			'<!DOCTYPE html>'.$CRLF.
			'<html lang="en">'.$CRLF.
			'<head>'.$CRLF.
			'<meta charset="utf-8" />'.$CRLF.
			'<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.$CRLF.
			'<meta name="viewport" content="width=device-width, initial-scale=1" />'.$CRLF.
			'<title>{{website title}}</title>'.$CRLF.

			// fav icon
			(empty($iconfile) ? '' :
				'<link rel="shortcut icon" href="{$iconfile}" type="image/x-icon" />'.$CRLF.
				'<link rel="icon" href="{$iconfile}" type="image/x-icon" />'.$CRLF
			).

			'<link rel="stylesheet" href="static/main.css" />'.$CRLF.
			'<link rel="stylesheet" href="static/bootstrap/dist/css/bootstrap.min.css" />'.$CRLF.
			'<script src="static/jquery/jquery.min.js"></script>'.$CRLF.
			'<script src="static/bootstrap/dist/js/bootstrap.min.js"></script>'.$CRLF.

			'<meta http-equiv="refresh" content="2" />'.$CRLF.

			'</head>'.$CRLF;
		$output .=
			'<body>'.$CRLF.
			'<nav class="navbar navbar-default">'.$CRLF.
			// container
			$TAB.'<!-- container -->'.$CRLF.
			$TAB.'<div class="container">'.$CRLF.
			// brand
			$TAB.$TAB.'<!-- brand -->'.$CRLF.
			$TAB.$TAB.'<div class="navbar-header">'.$CRLF.
			$TAB.$TAB.$TAB.'<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<span class="sr-only">Toggle navigation</span>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<span class="icon-bar"></span>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<span class="icon-bar"></span>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<span class="icon-bar"></span>'.$CRLF.
			$TAB.$TAB.$TAB.'</button>'.$CRLF.
			$TAB.$TAB.$TAB.'<a href="//schematicsforfree.com/" class="navbar-brand"><img src="static/gclogo.png" height="45" /></a>'.$CRLF.
			$TAB.$TAB.'</div><!-- /brand -->'.$CRLF.
			// nav links
			$TAB.$TAB.'<!-- nav links -->'.$CRLF.
			$TAB.$TAB.'<div class="collapse navbar-collapse" id="navbar">'.$CRLF.
			$TAB.$TAB.$TAB.'<ul class="nav navbar-nav">'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<li class="active"><a href="#">Home</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<li class="dropdown">'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">The Files <span class="caret"></span></a>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.'<ul class="dropdown-menu">'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.$TAB.'<li><a href="#">Audio</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.$TAB.'<li><a href="#">Filters</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.$TAB.'<li class="divider" role="separator"></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.$TAB.'<li><a href="#">Power</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.$TAB.'<li><a href="#">Video</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.$TAB.'</ul>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'</li>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<li><a href="#about">About</a></li>'.$CRLF.
			$TAB.$TAB.$TAB.'</ul>'.$CRLF.
			// search form
			$TAB.$TAB.$TAB.'<!-- search form -->'.$CRLF.
			$TAB.$TAB.$TAB.'<form method="GET" action="./" class="navbar-form navbar-right" id="navbar-search-form" role="search">'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<div class="form-group"><input type="text" class="form-control" id="navbar-search-text" placeholder="Search" /></div>'.$CRLF.
			$TAB.$TAB.$TAB.$TAB.'<button type="submit" class="btn btn-default" id="navbar-search-button"><span class="glyphicon glyphicon glyphicon-search"></span></button>'.$CRLF.
			$TAB.$TAB.$TAB.'</form>'.$CRLF.
			$TAB.$TAB.$TAB.'<!-- /search form -->'.$CRLF.
			$TAB.$TAB.'</div><!-- /navbar-collapse -->'.$CRLF.
			$TAB.'</div><!-- /container -->'.$CRLF.
			'</nav>'.$CRLF;
		$output .=
			'</body>'.$CRLF.
			'</html>';
//		$twig = $this->getTwig(__DIR__, 'test.htm');
		return $output;
	}



}
