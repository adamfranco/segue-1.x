<? /* $Id$ */

include("output_modules/common.inc.php");

$st = $o->getField("shorttext");

if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
if ($o->getField("texttype") == 'text') $st = htmlbr($st);

printc("<div>" . stripslashes($st));
if ($o->getField("discuss") || $o->getField("longertext")) {
	$link = "index.php?$sid&action=site&site=$site&section=$section&page=$page&story=".$o->id."&detail=".$o->id;
	//$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	//$link = "<a href='$link'>";
	if ($o->getField("longertext")) printc("<a href='".$link."'>"." ...more.</a>");
	printc("</div>");
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
