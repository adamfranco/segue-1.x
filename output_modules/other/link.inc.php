<? /* $Id$ */

include("output_modules/common.inc.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class=contentinfo align=right>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");
if ($o->getField("shorttext")) printc("<div class=desc>".stripslashes($o->getField("shorttext"))."</div>");
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