<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Item to Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	if ($_SESSION[pageObj]->getField("type") == "page")  printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");

if ($_SESSION[pageObj]->getField("type")=='link' || $_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class=title style='font-size: 14px'>Activation & Availability ".helplink("daterange")."</div>");

	$_SESSION[pageObj]->outputDateForm();
	
	printc("<br />");
	printc("<input type=radio name=active value=0 " . (($_SESSION[pageObj]->getField("active"))?"":" checked") . "> Hide ");
	printc("<input type=radio name=active value=1 " . (($_SESSION[pageObj]->getField("active"))?" checked":"") . "> Don't Hide</b> ");
	printc(" ".helplink("available")." (this overrides availability option above.)<br />");
}

printc("<br /><br />");


printc("</form>");

