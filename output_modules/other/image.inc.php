<? /* $Id$ */

include("output_modules/common.inc.php");

$st = $o->getField("shorttext");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 * replace general with specific
 ******************************************************************************/
$st = convertTagsToInteralLinks($site, $st);
if ($o->getField("texttype") == 'text')
	$st = nl2br($st);
	
$wikiResolver =& WikiResolver::instance();
$st = $wikiResolver->parseText($st, $site, $section, $page);


$filename = urldecode(db_get_value("media","media_tag","media_id='".addslashes($o->getField("longertext"))."'"));
$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id='".addslashes($o->getField("longertext"))."'");
$imagepath = "$uploadurl/$dir/$filename";
printc("\n\n<table align='center'>\n\t<tr>\n\t\t<td align='center'>\n\t\t\t<img src='$imagepath' border='0' alt='Full-size image' />\n\t\t</td>\n\t</tr>");

if ($o->getField("title")) {
	printc("\n\t<tr>\n\t\t<td align='center'>");
	printc("\n\t\t\t<strong>\n\t\t\t\t<a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id."'>");
	printc(spchars($o->getField("title")));
	printc("</a>\n\t\t\t</strong>\n\t\t</td>\n\t</tr>");
}

if ($o->getField("shorttext")) 
	printc("\n\t<tr>\n\t\t<td align='left'>".stripslashes($st)."</td>\n\t</tr>");
	
printc("\n</table>\n");
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}