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
<body bgcolor='white'>
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
	<li><a href='http://www.fckeditor.net/' target="new_window">FCKeditor</a>  -
	This is the new default HTML Editor for Segue.</li>
	
	<li><a href='http://sourceforge.net/projects/itools-htmlarea/' target="new_window">HTMLArea</a> by 
	<a href='http://www.interactivetools.com' target="new_window">Interactive Tools</a> -
	 HTMLArea is an HTML editor available within Segue.</li>
	 
	<li><a href='http://www.engageinteractive.com/flashSite/domit.html' target="new_window">DOMIT!</a> by 
	<a href='http://www.engageinteractive.com/' target="new_window">Engage Interactive</a> - DOMIT! XML parsing
	libraries are used to export and import Segue sites to and from XML files.</li>
	
	<li><a href='http://www.geckotribe.com/rss/carp/' target="new_window">CaRP: Caching RSS Parser</a> -
	The GPL version 3.5.2 (3/10/2004), with minor modifications by Adam Franco, is used to
	display RSS Feeds.</li>
	
	<li><a href='http://drupal.org' target="new_window">Drupal</a> / <a href='http://phpwiki.sf.net' target="new_window">PhpWiki</a> -
	Segue makes use of the <a href='http://cvs.drupal.org/viewcvs/drupal/contributions/modules/diff/DiffEngine.php' target="new_window">DiffEngine</a> from Drupal, a library for showing highlighted differences. This library was originally part of PhpWiki, but was reformatted when it was added to Drupal.</li>

	<li><a href='http://www.1pixelout.net/code/audio-player-wordpress-plugin/' target="new_window">1pixelout: Audio Player Wordpress plugin</a>  -
	Segue makes use of a Wordpress Flash audio player developed by <a href='http://www.1pixelout.net/' target="new_window">1pixelout</a>, based a <a href='http://www.macloo.com/examples/audio_player/' target="new_window">tutorial</a>
	written by <a href='http://mindymcadams.com/' target="new_window">Mindy Adams</a>
	</li>

</ul>

<h2>Thanks to:</h2>
<ul>
	<li /><b>Shel Sax</b> - Director of Educational Technology, Middlebury College.
	<li /><b>Reinhold Lange</b> - Web Programmer, ActiveX editor-help.
</ul>
</body>
</html>