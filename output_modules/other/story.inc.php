<? /* $Id$ */

include("output_modules/common.inc.php");

$st = $o->getField("shorttext");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 * replace general with specific
 ******************************************************************************/
$st = convertTagsToInteralLinks($site, $st);


if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
printc("<table cellspacing=0 cellpadding=0 width=100%><tr><td>");
printc(stripslashes($st));

if ($o->getField("discuss") || $o->getField("longertext")) {
	
	if ($o->getField("longertext") && !ereg("^[\n\r]*<br />$", $o->getField("longertext"))) {
		if ($action == 'viewsite')
			$discussAction = 'site';
		else if (ereg("preview_edit_as|preview_as", $action))
			$discussAction = ereg_replace("preview_edit_as", "preview_as", $action);
		else
			$discussAction = 'site';
			
		$link = "index.php?$sid&action=".$discussAction."&site=$site&section=$section&page=$page&story=".$o->id."&detail=".$o->id;
		printc("<a href='".$link."'>"." ...more.</a>");
	}
		
	include (dirname(__FILE__)."/discussionLink.inc.php");
	
}
printc("</td></tr></table><br>");
