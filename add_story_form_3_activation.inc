<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");

	printc("<div class=title style='font-size: 14px'>Activation & Availability ".helplink("daterange")."</div>");

	$_SESSION[storyObj]->outputDateForm();
/* 	printc("<table>"); */
/* 	printc("<tr><td align='right'>"); */
/* 	printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($_SESSION[settings][activatedate])?" checked":"")."> <select name='activateday'>"); */
/* 	for ($i=1;$i<=31;$i++) { */
/* 		printc("<option" . (($_SESSION[settings][activateday] == $i)?" selected":"") . ">$i\n"); */
/* 	} */
/* 	printc("</select>\n"); */
/* 	printc("<select name='activatemonth'>"); */
/* 	for ($i=0; $i<12; $i++) { */
/* 		printc("<option value=$i" . (($_SESSION[settings][activatemonth] == $i)?" selected":"") . ">$months[$i]\n"); */
/* 	} */
/* 	printc("</select>\n<select name='activateyear'>"); */
/* 	$curryear = date("Y"); */
/* 	for ($i=$curryear; $i <= ($curryear+5); $i++) { */
/* 		printc("<option" . (($_SESSION[settings][activateyear] == $i)?" selected":"") . ">$i\n"); */
/* 	} */
/* 	printc("</select>"); */
/* 	 */
/* 	printc("</td></tr>"); */
/* 	 */
/* 	printc("<tr><td align='right'>"); */
/* 	printc("Deactivate date:</td><td><input type=checkbox name='deactivatedate' value=1".(($_SESSION[settings][deactivatedate])?" checked":"")."> <select name='deactivateday'>"); */
/* 	for ($i=1;$i<=31;$i++) { */
/* 		printc("<option" . (($_SESSION[settings][deactivateday] == $i)?" selected":"") . ">$i\n"); */
/* 	} */
/* 	printc("</select>\n"); */
/* 	printc("<select name='deactivatemonth'>"); */
/* 	for ($i=0; $i<12; $i++) { */
/* 		printc("<option value=$i" . (($_SESSION[settings][deactivatemonth] == $i)?" selected":"") . ">$months[$i]\n"); */
/* 	} */
/* 	printc("</select>\n<select name='deactivateyear'>"); */
/* 	for ($i=$curryear; $i <= ($curryear+5); $i++) { */
/* 		printc("<option" . (($_SESSION[settings][deactivateyear] == $i)?" selected":"") . ">$i\n"); */
/* 	} */
/* 	printc("</select>"); */
/* 	 */
/* 	printc("</tr></td></table>"); */

//	printc("<br /><div class=desc>To prevent the page from being viewed at all, select <b>\"Disabled\"</b> below. This is useful to prevent people from viewing the site while it is still in progress. Selecting <b>\"Disabled\"</b> WILL OVER-RIDE the \"Activate On\" command, above. Selecting \"Enabled\" will not over-ride the \"[De]Activate On\" settings.</div>");

//	printc("<input type=radio name=active value=1 " . (($_SESSION[settings][active])?" checked":"") . "> Enabled | ");
//	printc("<input type=radio name=active value=0 " . (($_SESSION[settings][active])?"":" checked") . "> Disabled <br />");

	printc("<br /><br /><div class=title style='font-size: 14px'>Category</div>");
	printc("Category: <input type=text name=newcategory size=20 value='".$_SESSION[settings][newcategory]."'> <select name=category onChange='document.storyform.newcategory.value = document.storyform.category.value'>");
	printc("<option value=''".(($_SESSION[storyObj]->getField("category")=='')?" selected":"").">none");
	foreach ($_SESSION[settings][categories] as $c) {
		printc("<option value='$c'".(($_SESSION[storyObj]->getField("category")==$c)?" selected":"").">$c");
	}
	printc("</select>");
	
printc("<br /><br />");


printc("</form>");

