<? /* $Id$ */

include("output_modules/common.inc.php");

$st = $o->getField("shorttext");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 * replace general with specific
 ******************************************************************************/
//printpre($site);
//$specfic_mediapath = "http://segue.middlebury.edu";
$specfic_mediapath = $cfg[uploadurl]."/".$site;
$general_mediapath = "\[\[mediapath\]\]";
$st = eregi_replace($general_mediapath, $specfic_mediapath, $st);

// replace constant [[linkpath]] with specific link path (i.e. $full_uri)
$specfic_internal_linkpath = $cfg[full_uri];
$general_internal_linkpath = "\[\[linkpath\]\]";
$st = eregi_replace($general_internal_linkpath, $specfic_internal_linkpath, $st);
// replace general site reference with specific
$specfic_sitename = "site=".$site;
$general_sitename = "site=\[\[site\]\]"  ;
$st = eregi_replace($general_sitename, $specfic_sitename, $st);


if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
printc("<table cellspacing=0 cellpadding=0 width=100%><tr><td>");
printc(stripslashes($st));

if ($o->getField("discuss") || $o->getField("longertext")) {
	$link = "index.php?$sid&action=site&site=$site&section=$section&page=$page&story=".$o->id."&detail=".$o->id;
	//$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	//$link = "<a href='$link'>";
	if ($o->getField("longertext")) printc("<a href='".$link."'>"." ...more.</a>");
	
	printc("<div class=contentinfo align=right>");
	$l = array();
	//if ($o->getField("longertext")) $l[] = $link."Full Text</a>";
	if ($o->getField("discuss")) {
		$discusslabel = $o->getField("discusslabel");
		// check if discuss label exists for backward compatibility
		if ($discusslabel) {
			printc("<a href=".$link."#discuss>".$discusslabel."</a>");
		} else {
			printc("<a href=".$link."#discuss>Discuss</a>");
		}
		printc(" (".discussion::generateStatistics($o->id).")");	
	}
	printc(implode(" | ",$l));
	printc("</div>");
}
printc("</td></tr></table><br>");
