<? // output module

include("output_modules/common.inc.php");

if ($a[category]) {
	printc("<div class=contentinfo align=right>");
	printc("Category: <b>".spchars($a[category])."</b>");
	printc("</div>");
}

if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
printc("<div><a href='$a[url]' target='_blank'>$a[url]</a></div>");
if ($a[shorttext]) printc("<div class=desc>".stripslashes(urldecode($a[shorttext]))."</div>");
