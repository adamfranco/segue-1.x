<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='".$thisSite->name."'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add ".(($_SESSION[sectionObj]->getField("type") == 'section')?"Section":"Link")." to site \"".$thisSite->getField("title")."\"</div>");
else  printc("<div class=title>Edit Item \"".$_SESSION[sectionObj]->getField("title")."\" in Site \"".$thisSite->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");

if ($_SESSION[sectionObj]->getField("type")=='url' || $_SESSION[sectionObj]->getField("type")=='section') {
	printc("<div class=title style='font-size: 14px'>Activation & Availability ".helplink("daterange")."</div>");
	
	$_SESSION[sectionObj]->outputDateForm();

	printc("<br>");
	printc("<input type=radio name=active value=0 " . (($_SESSION[sectionObj]->getField("active"))?"":" checked") . "> Hide ");
	printc("<input type=radio name=active value=1 " . (($_SESSION[sectionObj]->getField("active"))?" checked":"") . "> Don't Hide</b> ");
	printc(" ".helplink("available")." (this overrides availability option.)<br>");
}

printc("<br><br>");


printc("</form>");

