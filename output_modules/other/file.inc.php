<? /* $Id$ */

include("output_modules/common.inc.php");

$t = makedownloadbar($o);
printc($t);
if ($o->getField("discuss")) {
	printc("<div class=contentinfo align=right>");
	$link = "index.php?$sid&action=site&site=$site&section=$section&page=$page&story=".$o->id."&detail=".$o->id;
	//$link = "index.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=".$o->id;
	//$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	$link = "<a href='$link'>";
	$l = array();
	if ($o->getField("discuss")) {
		if ($o->getField("discussdisplay") == 1) {
			$l[] = $link."discuss</a> (".discussion::generateStatistics($o->id).")";
		} else {
			$l[] = $link."assess</a> (".discussion::generateStatistics($o->id).")";	
		}
	}
	printc(implode(" | ",$l));
	printc("</div>");
}