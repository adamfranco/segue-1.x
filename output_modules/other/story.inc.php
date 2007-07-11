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

/******************************************************************************
 * print out story content
 ******************************************************************************/
printc("<div class='story'>");
printc(stripslashes($st));
printc("</div>");

/******************************************************************************
 * Append link to more and discussion
 ******************************************************************************/

if ($o->getField("discuss") || $o->getField("longertext")) {
	
	if ($o->getField("longertext") && !ereg("^[\n\r]*<br />$", $o->getField("longertext"))) {
		if ($action == 'viewsite')
			$discussAction = 'viewsite';
		else if (ereg("preview_edit_as|preview_as", $action))
			$discussAction = ereg_replace("preview_edit_as", "preview_as", $action);
		else
			$discussAction = 'site';
			
		$link = "index.php?$sid&amp;action=".$discussAction."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id;
		printc("<a href='".$link."'>"." ...more</a>\n");

	}
	include (dirname(__FILE__)."/discussionLink.inc.php");
	
}



if ($tagged_section) $section = $source_section;
if ($tagged_page) $page = $source_page;