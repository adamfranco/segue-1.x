<? // add_site_form_1_title.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");
printc("<input type=hidden name='step'>");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
/* 	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n"); */
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");

if ($ltype=='admin') {
	printc("name: ".$_SESSION[siteObj]->getField("name"));
	printc("<br />type: ".$_SESSION[siteObj]->getField("type"));
	printc("<br /><br />");

/* 	printc("name: <input type='text' size=20 name=sitename value='".$_SESSION[siteObj]->getField("name")."'>"); */
/* 	printc("<br />type:"); */
/* 	printc("<br /><input type=radio name=type value='class'".(($_SESSION[siteObj]->getField("type")=='class')?" checked":"")."> class"); */
/* 	printc("<br /><input type=radio name=type value='personal'".(($_SESSION[siteObj]->getField("type")=='personal')?" checked":"")."> personal"); */
/* 	printc("<br /><input type=radio name=type value='system'".(($_SESSION[siteObj]->getField("type")=='system')?" checked":"")."> system"); */
/* 	printc("<br /><input type=radio name=type value='other'".(($_SESSION[siteObj]->getField("type")=='other')?" checked":"")."> other"); */
/* 	printc("<br /><br />"); */
} else
	printc("<input type=hidden name=sitename value='".$_SESSION[siteObj]->getField("name")."'>");

printc ("<div class='desc'>");
if ($_SESSION[settings][add]) printc ("On this and the following pages, you can specify the settings for your new site. Except for the template, you can come back and change every one of these settings after you have created your site.");
if ($_SESSION[settings][edit]) printc ("Click the Next/Previous Buttons or the links to the left to navigate through the site settings widows. <br />Clicking \"Cancel\" will drop all changes without saving.");
printc("</div>");

printc("<br />");
printc("<br />");

printc("<div class=title>1. Title</div><br />");
//printc("<div class=desc>Enter the site <b>title</b>. This is required.</div>");

printc("Title: <input type='text' size=35 name='title' value='".spchars($_SESSION[siteObj]->getField("title"))."'>");
printc("<span class=editnote> *required</span>");
printc("<br />");

if (!isclass($_SESSION[siteObj]->getField("name"))) printc("<br /><br /><input type=checkbox name='listed' value=1".(($_SESSION[siteObj]->getField("listed"))?" checked":"")."> Include site in public listing.".helplink("listing"));
else printc("<input type=hidden name='listed' value=1>");
printc("<br /><br />");

/* ------------------------------------------------------------ */
/* this will be replace by our new permissions system -- hence the comments */
/* Actually, this will be in addition to the new system, and interface with it. - Adam 2003:02 */
/******************************************************************************
 * Anyone:		Should be checked if 'everyone' has full view permissions.
 * Institute:	Should be checked if 'everyone' has no permissions and
 *				'intitute' has full view permissions.
 * Class:		Should be ckecked if 'everyone' and 'institute' have no 
 *				permissions and a user with the same name as the site, the 
 *				class, has full view permissions.
 * Custom:		Checked if the others fail and in edit mode.
 ******************************************************************************/
/* print "Everyone:".$_SESSION[siteObj]->hasPermission("view","everyone","1")."<br />"; */
/* print "EveryoneDirect:".$_SESSION[siteObj]->permissions[everyone][3]."<br />"; */
/* print "EveryoneDown:".$_SESSION[siteObj]->hasPermissionDown("view","everyone","1")."<br />"; */
/* print "<br />"; */
/* print "Institute:".$_SESSION[siteObj]->hasPermission("view","institute","1")."<br />"; */
/* print "InstituteDirect:".$_SESSION[siteObj]->permissions[institute][3]."<br />"; */
/* print "InstituteDown:".$_SESSION[siteObj]->hasPermissionDown("view","institute","1")."<br />"; */
/* print "<br />"; */
/* print "Class:".$_SESSION[siteObj]->hasPermission("view",$_SESSION[siteObj]->getField("name"),"1")."<br />"; */
/* print "ClassDirect:".$_SESSION[siteObj]->permissions[$_SESSION[siteObj]->getField("name")][3]."<br />"; */
/* print "--------------------------------<br />"; */


if ($_SESSION[settings][viewpermissions] == "custom") {
	$whocanview = "4";
} else if ($_SESSION[siteObj]->hasPermission("view","everyone","1")) {
	$whocanview = "1";
} else if ($_SESSION[siteObj]->hasPermission("view","institute","1") && !$_SESSION[siteObj]->hasPermissionDown("view","everyone","1")) {
	 $whocanview = "2";
} else if ($_SESSION[settings][className]) {
	if ($_SESSION[siteObj]->hasPermission("view",$_SESSION[settings][className],"1") && !$_SESSION[siteObj]->hasPermissionDown("view","institute","1") && !$_SESSION[siteObj]->hasPermissionDown("view","everyone","1")) $whocanview = "3";
	else  $whocanview = "4";
} else {
	 $whocanview = "4";
}

printc("Allow: <b><input type=radio name=viewpermissions value='everyone'".(($whocanview == "1")?" checked":"")."> anyone ");
printc(" <input type=radio name=viewpermissions value='institute'".(($whocanview == "2")?" checked":"")."> $cfg[inst_name] users ");
if ($_SESSION[siteObj]->getField("type") == "class") {
	printc(" <input type=radio name=viewpermissions value='class'");
	if ($_SESSION[siteObj]->isEditor($_SESSION[settings][className])) {
		if ($whocanview == "3")
			printc(" checked");
	}
	printc("> students in class");

	if ($_SESSION[settings][edit]) {
		printc(" <input type=radio name=viewpermissions value='custom'");
		if ($whocanview == "4") {
			printc(" checked");
		}
		printc("> custom");
	}
} else {
	if ($_SESSION[settings][edit]) {
		printc(" <input type=radio name=viewpermissions value='custom'");
		if ($whocanview == "4") {
			printc(" checked");
		}
		/* the permissions window does not seem to save properly. add in later. */
/* 		printc(" onClick='sendWindow(\"permissions\",600,400,\"edit_permissions.php?$sid&site=".$_SESSION[siteObj]->getField("name")."\")'> custom"); */
		printc("> custom");
	}
}

printc("</b> to view this site.<br /><br />");

printc("<div class=title>2. When is this site available?".helplink("daterange")." &nbsp; - &nbsp; <i>Optional</i></div>");
//printc("<div class=desc>If you would like this site to be available only during a certain range of dates, enter them below and select the checkbox to the left of that date.</div>");

$_SESSION[siteObj]->outputDateForm();

printc("<br />");

printc("<div class=title>3. Hide my site".helplink("available")."</div>");
//printc("<div class=desc>To prevent the site from being viewed at all, select <b>\"Disabled\"</b> below. This is useful to prevent people from viewing the site while it is still in progress. Selecting <b>\"Disabled\"</b> WILL OVER-RIDE the \"Activate On\" command, above. Selecting \"Enabled\" will not over-ride the \"[De]Activate On\" settings.</b></div>");
printc("<br />");

printc("<input type=radio name=active value=0 " . (($_SESSION[siteObj]->getField("active"))?"":" checked") . "> Hide ");
printc("<input type=radio name=active value=1 " . (($_SESSION[siteObj]->getField("active"))?" checked":"") . "> Don't Hide  <br /></b>");
printc("<br />(this overrides availability option above)<br />");

if ($_SESSION[settings][edit]) {
	printc("<p><div class=desc>");
	printc("<input type=checkbox name=recursiveenable value=1".(($_SESSION[settings][recursiveenable])?" checked":"").">");
	printc("Check this box if you would like to hide/un-hide (depending on what you have selected above) every part of this site (sections and pages).");
	printc(" This is useful if you have hidden or un-hidden various sections or pages of your site and want to reset all of the \"Hide\" settings");
	printc("</div>");
}

printc("<br />");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
/* 	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n"); */
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");

printc("</form>");