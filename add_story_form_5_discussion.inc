<? /* $Id$ */
if ($_REQUEST[goback]) $_SESSION[settings][goback] = $_REQUEST[goback];
//printpre($_SESSION[settings]);

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


printc("<div class=title>Discussion/Assessment</div>");
printc("<div class=desc>You have the option of allowing visitors to your site discuss or assess certain content contained in it. You can restrict discussion access to students in the class (if this is a class website), only $cfg[inst_name] users, or anyone (allowing for anonymous discussion).</div>");
printc("<div class=leftmargin>");
$discuss = $_SESSION[storyObj]->getField("discuss");
$discussemail = $_SESSION[storyObj]->getField("discussemail");
$discusslabel = $_SESSION[storyObj]->getField("discusslabel");
$discussdisplay = $_SESSION[storyObj]->getField("discussdisplay");
$discussauthor = $_SESSION[storyObj]->getField("discussauthor");
printc("<table cellpadding=3 cellspacing=0>");
printc("<tr>");
printc("<td valign=top><input type=checkbox value='1' name=discuss".(($discuss == 1)?" checked":"")."></td>");
printc ("<td valign=top><b>Enable</b> discussion/assessment: Check this to allow this content to be the subject of discussion or assessment</td>");
printc("<td valign=top><input type=checkbox value='1' name=discussemail".(($discussemail == 1)?" checked":"")."></td>");
printc ("<td valign=top><b>Email</b> all posts: Check this to receive an email of every post.</td>");
printc("</tr><tr>");
printc("<td valign=top><input type=radio value='1' name=discussauthor".(($discussauthor == 1 || !$discussauthor)?" checked":"")."></td>");
printc("<td valign=top><b>Show Authors:</b> Select this to allow for normal discussion whereby the author of each post is identified.</td>");
//printc("</tr><tr>");
printc("<td valign=top><input type=radio value='2' name=discussauthor".(($discussauthor == 2)?" checked":"")."></td>");
printc("<td valign=top><b>Hide Authors:</b> Select this to allow for anonymous discussion.  Only the site owner will know the author of each post (this is also useful for the display and/or discussion of assessment results).</td>");
printc("</tr><tr>");
printc("<td valign=top><input type=radio value='1' name=discussdisplay".(($discussdisplay == 1 || !$discussdisplay)?" checked":"")."></td>");
printc("<td valign=top><b>Discussion:</b> Participants can read and respond to each other's posts.</td>");
//printc("</tr><tr>");
printc("<td valign=top><input type=radio value='2' name=discussdisplay".(($discussdisplay == 2)?" checked":"")."></td>");
printc("<td valign=top><b>Assessment:</b> Participants will <b>not</b> be able to read each other's posts (assessment posts can be made available for discussion after assessment is completed). </td>");
printc("</tr>");
printc("</table><br>");
printc("Specify discussion/assessment link label:");
printc("<div class=desc>(This is the label for the link that participants use to access discussion/assessment)</div>");
printc("<table width=100% cellpadding=3 cellspacing=0 style='border: 1px solid gray'>");
printc("<tr>");
printc("<td valign=top><input type=radio value='Discuss' name=discusslabel".(($discusslabel == "Discuss" || !$discusslabel)?" checked":"")."> Discuss</td>");
printc("<td valign=top><input type=radio value='Assess' name=discusslabel".(($discusslabel == "Assess")?" checked":"")."> Assess</td>");
printc("<td valign=top><input type=radio value='Comment' name=discusslabel".(($discusslabel == "Comment")?" checked":"")."> Comment</td>");
printc("<td valign=top><input type=radio value='Review' name=discusslabel".(($discusslabel == "Review")?" checked":"")."> Review</td>");
printc("<td valign=top><input type=radio value='Register' name=discusslabel".(($discusslabel == "Register")?" checked":"")."> Register</td>");
//printc("</tr><tr>");
//printc("<td valign=top colspan=5><input type=radio value='1' name=label".(($label == "Other")?" checked":"")."> Other <input type=textfield value='' name=label></td>");
//printc("</tr>");
printc("</table><br>");

//printpre($_SESSION);



if ($_SESSION[auser] == $site_owner) {
	$canAddEditors = true;
} else {
	$canAddEditors = false;
}

printc("Specify who can discuss/assess this content:");
printc("<div class=desc>(Giving everyone permission will require participants to register.  Limit permissions to ".$cfg[inst_name]." or specified groups to ensure proper authentication)</div>");

permissions::outputForm($_SESSION[storyObj],1,$canAddEditors);

printc("</div>");
printc("<br>");

printc("<br><br>");


printc("</form>");

