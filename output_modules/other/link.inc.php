<? /* $Id$ */

include("output_modules/common.inc.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class=contentinfo align=right>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

/* print "<pre>"; */
/* print_r($o->data); */
/* print "</pre>"; */

/* print db_get_value("story","story_text_long","story_id=".$o->id); */

$abbrurl = substr($o->getField("url"),0,75);
if ($o->getField("title")) {
	printc("<div class=leftmargin><b><a href='".$o->getField("url")."' target='_blank'>");
	printc(spchars($o->getField("title"))."</a></b></div>");
}
printc("<div class=desc><a href='".$o->getField("url")."' target='_blank'>".$abbrurl."...</a></div>");
//printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");

if ($o->getField("shorttext")) printc("<div class=desc>".stripslashes($o->getField("shorttext"))."</div>");
if ($o->getField("discuss")) {
	printc("<div class=contentinfo align=right>");
	$link = "index.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=".$o->id;
	//$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	//$link = "<a href='$link'>";
	$l = array();
	if ($o->getField("discuss")) {
		$discusslabel = $o->getField("discusslabel");
		printc("<a href=".$link."#discuss>".$discusslabel."</a> (".discussion::generateStatistics($o->id).")");
	}
/* 	if ($o->getField("longertext")) $l[] = $link."full text</a>"; */
	printc(implode(" | ",$l));
	printc("</div>");
}