<? /* $Id$ */

include("output_modules/common.inc.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class=contentinfo align='right'>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

/* print "<pre>"; */
/* print_r($o->data); */
/* print "</pre>"; */

/* print db_get_value("story","story_text_long","story_id=".$o->id); */

$abbrurl = substr($o->getField("url"),0,75);
$url = $o->getField("url");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 * replace general with specific
 ******************************************************************************/
$url = convertTagsToInteralLinks($site, $url);

$abbrurl = substr($url,0,75);


if ($o->getField("title")) {
	printc("<div class=leftmargin><b><a href='".$url."' target='_blank'>");
	printc(spchars($o->getField("title"))."</a></b></div>");
}
printc("<div class=desc><a href='".$url."' target='_blank'>".$abbrurl."...</a></div>");
//printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");

if ($o->getField("shorttext")) printc("<div class=desc>".stripslashes($o->getField("shorttext"))."</div>");
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}