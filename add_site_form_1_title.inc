<script lang="JavaScript">

function submitForm() {
//	document.forms[0].submit();
//	document.forms['addform'].submit();
	document.addform.submit();	// IE for Mac does not like this reference.
}

</script>


<? // add_site_form_1_title.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");
if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");


if ($ltype=='admin') {
	printc("name: <input type=text size=20 name=sitename value='".$_SESSION[siteObj]->getField("name")."'>");
	printc("<br>type:");
	printc("<br><input type=radio name=type value='class'".(($_SESSION[siteObj]->getField("type")=='class')?" checked":"")."> class");
	printc("<br><input type=radio name=type value='personal'".(($_SESSION[siteObj]->getField("type")=='personal')?" checked":"")."> personal");
	printc("<br><input type=radio name=type value='system'".(($_SESSION[siteObj]->getField("type")=='system')?" checked":"")."> system");
	printc("<br><input type=radio name=type value='other'".(($_SESSION[siteObj]->getField("type")=='other')?" checked":"")."> other");
	printc("<BR><BR>");
} else
	printc("<input type=hidden name=sitename value='".$_SESSION[siteObj]->getField("name")."'>");

printc ("<div class='desc'>");
if ($_SESSION[settings][add]) printc ("On this and the following pages, you can specify the settings for your new site. Except for the template, you can come back and change every one of these settings after you have created your site.");
if ($_SESSION[settings][edit]) printc ("Click the Next/Previous Buttons or the links to the left to navigate through the site settings widows. <br>Clicking \"Cancel\" will drop all changes without saving.");
printc("</div>");

printc("<br>");
printc("<br>");

printc("<div class=title>1. Title</div><br>");
//printc("<div class=desc>Enter the site <b>title</b>. This is required.</div>");

printc("Title: <input type=text size=35 name='title' value='".spchars($_SESSION[siteObj]->getField("title"))."'>");
printc("<span class=editnote> *required</span>");
printc("<br><br><br>");


printc("<div class=title>2. Who do you want to be able to view this site?</div>");

/* ------------------------------------------------------------ */
/* this will be replace by our new permissions system -- hence the comments */
//printc("Allow: <b><input type=radio name=viewpermissions value='anyone'".(($_SESSION[settings][viewpermissions]=='anyone')?" checked":"")."> anyone ");
//printc("<input type=radio name=viewpermissions value='midd'".(($_SESSION[settings][viewpermissions]=='midd')?" checked":"")."> $cfg[inst_name] users ");
//if (isclass($_SESSION[settings][sitename])) printc("<input type=radio name=viewpermissions value='class'".(($_SESSION[settings][viewpermissions]=='class')?" checked":"")."> students in class");

//printc("</b> to view this site.");
if (!isclass($_SESSION[siteObj]->getField("name"))) printc("<br><br><input type=checkbox name='listed' value=1".(($_SESSION[siteObj]->getField("listed"))?" checked":"")."> Include site in public listing.".helplink("listing"));
else printc("<input type=hidden name='listed' value=1>");
printc("<br><br><br>");


printc("<div class=title>3. When is this site available?".helplink("daterange")." &nbsp; - &nbsp; <i>Optional</i></div>");
//printc("<div class=desc>If you would like this site to be available only during a certain range of dates, enter them below and select the checkbox to the left of that date.</div>");

printc("<table>");
printc("<tr>");
printc("<td align=right>");
printc("Make site available beginning: </td><td><input type=checkbox name='activatedate' value=1".(($_SESSION[settings][activatedate])?" checked":"")."> <select name='activateday'>");
for ($i=1;$i<=31;$i++) {
	printc("<option" . (($_SESSION[settings][activateday] == $i)?" selected":"") . ">$i\n");
}
printc("</select>\n");
printc("<select name='activatemonth'>");
for ($i=0; $i<12; $i++) {
	printc("<option value=$i" . (($_SESSION[settings][activatemonth] == $i)?" selected":"") . ">$months[$i]\n");
}
printc("</select>\n<select name='activateyear'>");
$curryear = date("Y");
for ($i=$curryear; $i <= ($curryear+5); $i++) {
	printc("<option" . (($_SESSION[settings][activateyear] == $i)?" selected":"") . ">$i\n");
}
printc("</select>");
printc("</td></tr>");

printc("<tr>");
printc("<td align=right>");
printc("Make site unavailable after: </td><td><input type=checkbox name='deactivatedate' value=1".(($_SESSION[settings][deactivatedate])?" checked":"")."> <select name='deactivateday'>");
for ($i=1;$i<=31;$i++) {
	printc("<option" . (($_SESSION[settings][deactivateday] == $i)?" selected":"") . ">$i\n");
}
printc("</select>\n");
printc("<select name='deactivatemonth'>");
for ($i=0; $i<12; $i++) {
	printc("<option value=$i" . (($_SESSION[settings][deactivatemonth] == $i)?" selected":"") . ">$months[$i]\n");
}
printc("</select>\n<select name='deactivateyear'>");
for ($i=$curryear; $i <= ($curryear+5); $i++) {
	printc("<option" . (($_SESSION[settings][deactivateyear] == $i)?" selected":"") . ">$i\n");
}
printc("</select>");
printc("</td></tr></table>");
printc("<br>");

printc("<div class=title>4. Hide my site".helplink("available")."</div>");
//printc("<div class=desc>To prevent the site from being viewed at all, select <b>\"Disabled\"</b> below. This is useful to prevent people from viewing the site while it is still in progress. Selecting <b>\"Disabled\"</b> WILL OVER-RIDE the \"Activate On\" command, above. Selecting \"Enabled\" will not over-ride the \"[De]Activate On\" settings.</b></div>");
printc("<br>");

printc("<input type=radio name=active value=0 " . (($_SESSION[siteObj]->getField("active"))?"":" checked") . "> Hide ");
printc("<input type=radio name=active value=1 " . (($_SESSION[siteObj]->getField("active"))?" checked":"") . "> Don't Hide  <br></b>");
printc("<br>(this overrides availability option above)<br>");

if ($_SESSION[settings][edit]) {
	printc("<p><div class=desc>");
	printc("<input type=checkbox name=recursiveenable value=1".(($_SESSION[settings][recursiveenable])?" checked":"").">");
	printc("Check this box if you would like to enable/disable (depending on what you have selected above) every part of this site (sections and pages).");
	printc(" This is useful if you have enabled or disabled various sections or pages of your site and want to reset all of the \"enable\" settings");
	printc("</div>");
}

printc("<br>");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");

printc("</form>");