<?php

/**
 * Segue credits
 *
 * @version $Id$
 * @copyright 2003 
 **/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body {
	font-family: 'Verdana';
	font-size: 11px;
	color: #444;
}

a {
	text-decoration: none;
	color: #336;
	font-weight: bolder;
}

a:hover {
	color: #933;
	text-decoration: underline;
}

h1 { font-size: 20px; font-variant: small-caps; }
h2 { font-size: 16px; padding-left: 3px; border-bottom: 1px dotted #888; padding-bottom: 2px; }

li { margin-bottom: 3px; }
</style>
</head>
<body bgcolor=white>
<h1>
<a href='http://segue.sourceforge.net/' target="_blank">Segue</a> 
(<a href='changelog/changelog.html' target='_blank'>v. <?=file_get_contents("version.txt");?></a>)
</h1>

<h2>Developers</h2>

<ul>
	<li /><a href='mailto: gschine at middlebury dot edu'>Gabriel Schine</a> -
	Chief Architect. student, Kenyon College, Ohio.
	<li /><a href='mailto: achapin at middlebury dot edu'>Alex Chapin</a> -
	Project Manager. Educational Technologist, Middlebury College.
	<li /><a href='mailto: afranco at middlebury dot edu'>Adam Franco</a> -
	Full-time Programmer. Educational Technology Assistant, Middlebury College.
	<li /><a href='mailto: dradichk at middlebury dot edu'>Dobo Radichkov</a> - 
	Part-time Programmer, DBA. Educational Technology Assistant and student, Middlebury College.
</ul>

<h2>Included/Referenced Projects</h2>

<ul>
	<li /><a href='http://sourceforge.net/projects/itools-htmlarea/'>HTMLArea</a> by 
	<a href='http://www.interactivetools.com'>Interactive Tools</a> -
	The WYSIWYG editor in Segue is HTMLArea with some minor modifications.
	<li /><a href='http://www.engageinteractive.com/flashSite/domit.html'>DOMIT!</a> by 
	<a href='http://www.engageinteractive.com/'>Engage Interactive</a> - DOMIT! XML parsing
	libraries are used to export and import Segue sites to and from XML files.
	<li /><a href='http://www.geckotribe.com/rss/carp/'>CaRP: Caching RSS Parser</a> -
	The GPL version 3.5.2 (3/10/2004), with minor modifications by Adam Franco, is used to
	display RSS Feeds.
</ul>

<h2>Thanks to:</h2>
<ul>
	<li /><b>Shel Sax</b> - Director of Educational Technology, Middlebury College.
	<li /><b>Reinhold Lange</b> - Web Programmer, ActiveX editor-help.
</ul>
</body>
</html>