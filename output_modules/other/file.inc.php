<? /* $Id$ */

include("output_modules/common.inc.php");

$t = makedownloadbar($o);
printc($t);
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