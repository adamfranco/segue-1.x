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
