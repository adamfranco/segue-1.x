<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
//	if ($_SESSION[settings][type] == "page") printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


	if ($_SESSION[auser] == $site_owner || $thisPage->getField("ediscussion")==1) {
		printc("<div class=title>Discussion</div>");
		printc("<div class=desc>You have the option of allowing visitors to your site discuss certain content contained in it. You can restrict discussion access to students in the class (if this is a class website), only $cfg[inst_name] users, or anyone (allowing for anonymous discussion).</div>");
		printc("<div class=leftmargin>");
		printc("Enable discussion for this story: <input type=checkbox value='1' name=discuss".(($_SESSION[storyObj]->getField("discuss"))?" checked":"")."><br>");
		printc("Who should be able to discuss this story? <BR>");
		permissions::outputForm($_SESSION[storyObj],1);
		printc("</div>");
		printc("<br>");
	} else {
		printc("<input type=hidden name=discuss value=".$_SESSION[storyObj]->getField("discuss").">");
	}

printc("<br><br>");


printc("</form>");

