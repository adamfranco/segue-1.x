<? /* $Id$ */

include("output_modules/common.inc.php");

$filename = urldecode(db_get_value("media","media_tag","media_id=".$o->getField("longertext")));
$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=".$o->getField("longertext"));
$imagepath = "$uploadurl/$dir/$filename";
printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
if ($o->getField("title")) printc("<tr><td align=center><b>".spchars($o->getField("title"))."</b></td></tr>");
if ($o->getField("shorttext")) printc("<tr><td align=left>".stripslashes($o->getField("shorttext"))."</td></tr>");
printc("</table>");
if ($o->getField("discuss")) {
	printc("<div class=contentinfo align=right>");
	$link = "index.php?$sid&action=site&site=$site&section=$section&page=$page&story=".$o->id."&detail=".$o->id;
	//$link = "index.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=".$o->id;
	//$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	//$link = "<a href='$link'>";
	$l = array();
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