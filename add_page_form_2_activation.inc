<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Item to Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	if ($_SESSION[pageObj]->getField("type") == "page") printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


if ($_SESSION[pageObj]->getField("type")=='url' || $_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class=title style='font-size: 14px'>Activation & Availability</div>");

	$_SESSION[pageObj]->outputDateForm();
	
	printc("<br><div class=desc>To prevent the page from being viewed at all, select <b>\"Disabled\"</b> below. This is useful to prevent people from viewing the site while it is still in progress. Selecting <b>\"Disabled\"</b> WILL OVER-RIDE the \"Activate On\" command, above. Selecting \"Enabled\" will not over-ride the \"[De]Activate On\" settings.</div>");

	printc("<input type=radio name=active value=1 " . (($_SESSION[pageObj]->getField("active"))?" checked":"") . "> Enabled | ");
	printc("<input type=radio name=active value=0 " . (($_SESSION[pageObj]->getField("active"))?"":" checked") . "> Disabled <br>");
}

printc("<br><br>");


printc("</form>");

