<? /* $Id$ */ ?>
<script lang="JavaScript">

function submitForm() {
	document.addform.submit();	
}

</script>

<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

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
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	if ($_SESSION[storyObj]->getField("type") == 'story') printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");



/* 	printc("<div class=title style='font-size: 14px'>Permissions (for editing page content)".helplink("editors")." &nbsp; - &nbsp; <i>Optional</i></div>"); */
/* 	printc("<div class=desc>Here you can designate people for whom you wish to give editing access to this page. No access is given unless an add/edit/delete box is checked.</div>"); */

	printc("<div class=leftmargin>");
	
	// output permissions table
	//permissions::outputForm($_SESSION[storyObj]);	
	
//	printc("<br>Allow editor discussion enabling: <input type=checkbox name='ediscussion'". (($_SESSION[settings][ediscussion])?" checked":"")."><br>");
//	printc("<div class=desc>The above checkbox dictates if editors will be allowed to activate or deactivate discussion on specific content in this page.</div>");
		
	if ($site_owner == $_SESSION[auser]) {
		printc("<br><br>Locked: <input type=checkbox name=locked value=1".(($_SESSION[storyObj]->getField("locked"))?" checked":"").">");
		printc("<div class=desc>By checking the above box, you are preventing any editors from editing this content block, regardless of their editor privileges.</div>");
		printc("<br>");
	} else printc("<input type=hidden name=locked value=".$_SESSION[storyObj]->getField("locked").">");
			
	printc("</div>");


printc("<br><br>");

printc("</form>");

