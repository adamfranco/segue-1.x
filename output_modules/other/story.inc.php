<? /* $Id$ */

include("output_modules/common.inc.php");

$st = $o->getField("shorttext");

if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
if ($o->getField("texttype") == 'text') $st = htmlbr($st);

printc("<div>" . stripslashes($st) . "</div>");
if ($o->getField("discuss") || $o->getField("longertext")) {
	printc("<div class=contentinfo align=right>");
	$link = "fullstory.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=".$o->id;
	$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	$l = array();
	if ($o->getField("discuss")) $l[] = $link."discussions</a>";
	if ($o->getField("longertext")) $l[] = $link."full text</a>";
	printc(implode(" | ",$l));
	printc("</div>");
}
