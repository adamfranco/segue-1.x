
<script lang="JavaScript">

function submitForm() {
	document.addform.submit();	
}

</script>

<? // add_site_form_4_permissions.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");
if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

// --- title ---
printc("<div class=title>".(($_SESSION[settings][edit])?"5":"6").". Editing Permissions".helplink("editors")." &nbsp; - &nbsp; <i>Optional</i></div>");
printc("<div class=desc>In this section you can designate people for whom you may later give section-level or page-level editing access.</div>");

// --- form contents ---

permissions::outputForm($_SESSION[siteObj]);


printc("<br><br><div class=desc>By checking \"Add\", \"Edit\", or \"Delete\" on here, you give the editor site-level permissions. Site-level permissions allow the Editor to either Add, Edit (includes changing the order of), or Delete sections of the site. Editors with permissions specified here will not have section-level or page-level permissions unless the \"Copy Down Permissions\" box is checked.</div>");

/*if ($_SESSION[settings][edit])*/ printc("<div class=desc><b>NOTE:</b> if you would like Segue to copy the permissions for a certain user down to all sections and pages contained in this site, check the box to the left of their name. You also have the option later to lock sections of the site, preventing any editor access.</div>");
//if ($_SESSION[settings][editors]) printc("<div class=desc>Example: if you want <i>$e</i> to be able to edit the settings (everything but the actual content) of sections in this site, check the <b>Edit</b> box associated with <i>$e</i>'s name. You also have the option later to lock sections of the site, preventing any editor access.</div>");	
// end permissions table

printc("</div>");
printc("<br>");
printc("<br>");

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");

printc("</form>");


