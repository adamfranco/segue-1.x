<? /* $Id$ */

include("output_modules/common.inc.php");

include_once (dirname(__FILE__)."/carprss/carp.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class=contentinfo align='right'>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

//  print "<pre>"; 
//  print_r($o->data); 
//  print "</pre>";
 
 ob_start();
 CarpCacheShow($o->getField("url"));
 printc (ob_get_contents());
 ob_clean();


if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
// printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");
// if ($o->getField("shorttext")) printc("<div class=desc>".stripslashes($o->getField("shorttext"))."</div>");
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}