<? /* $Id$ */

include("output_modules/common.inc.php");

$filename = urldecode(db_get_value("media","name","id=".$o->getField("longertext")));
$dir = db_get_value("media","site_id","id=".$o->getField("longertext"));
$imagepath = "$uploadurl/$dir/$filename";
printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
if ($o->getField("title")) printc("<tr><td align=center><b>".spchars($o->getField("title"))."</b></td></tr>");
if ($o->getField("shorttext")) printc("<tr><td align=left>".stripslashes($o->getField("shorttext"))."</td></tr>");
printc("</table>");
if ($o->getField("discuss")) {
	printc("<div class=contentinfo align=right>");
	$link = "fullstory.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=".$o->id;
	$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	$l = array();
	if ($o->getField("discuss")) $l[] = $link."discussions</a>";
	/* if ($o->getField("longertext")) $l[] = $link."full text</a>"; */
	printc(implode(" | ",$l));
	printc("</div>");
}