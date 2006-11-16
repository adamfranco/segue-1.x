<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method='post' name='addform'>");
printc("<input type='hidden' name='step'>");

//printc("<input type='hidden' name='site' value='".$thisSite->name."'>");
//printc("<input type='hidden' name='section' value='$_SESSION[settings][section]'>");
//printc("<input type='hidden' name='page' value='$_SESSION[settings][page]'>");
//if ($edit) printc("<input type='hidden' name='edit_page' value='$_SESSION[settings][page]'>");
printc("<input type='hidden' name='action' value='$action'>");

//printc("<input type='hidden' name='mode' value='$mode'>");
printc("<input type='hidden' name='typeswitch' value='0'>");

if ($_SESSION[settings][add]) printc("<div class='title'>Add ".(($_SESSION[sectionObj]->getField("type") == 'section')?"Section":"Link")." to site \"".$thisSite->getField("title")."\"</div>");
else  printc("<div class='title'>Edit Item \"".$_SESSION[sectionObj]->getField("title")."\" in Site \"".$thisSite->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
	printc("<input type='button' value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width='50%' align='right'>");
	printc("<input type='button' value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type='button' value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type='submit' name='save' value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel'>");
printc("<input type='hidden' value='0' name='prevbutton'>");
printc("<input type='hidden' value='0' name='nextbutton'>");

if ($_SESSION[sectionObj]->getField("type")=='section') {

	printc("<div class='title'>Display Options</div>");
	printc("Hide Sidebar: <input type='checkbox' name='hide_sidebar' value='1'". (($_SESSION[sectionObj]->getField("hide_sidebar") == 1)?" checked='checked'":"") . "><br />");
	printc("<div class='desc'>");
	printc("Hide sidebars will hide both the left and right sidebars in all themes (EXCEPT Bevel Box and Tabs).");
	printc("  Links to pages and other sidebar content will be shown in the upper right corner. <br><br>");
	printc("<i>Chose to hide sidebars if you want to use the full width of the page or create a sequence of pages</i>");
	printc("</div>");

}

printc("<br /><br />");


printc("</form>");

