<? /* $Id$ */ ?>
<script lang="JavaScript">
//var theme = document.addform.theme.value;

function selectRow(row) {
	if (row > 1) {
		action = document.addform.action;
		document.addform.action=action+"#row"+row;
	}
	document.addform.submit();
}

function doTemplateWin(template) {
	//var template = document.addform.template.value;
	var theme = "<? print $_SESSION[siteObj]->getField("theme"); ?>";
	var themesettings = "<? print $_SESSION[siteObj]->getField("themesettings"); ?>";
	if (template != '') {
		var temp = window.open("index.php?action=site&site="+template+"&themesettings="+themesettings+"&theme="+theme,"template","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=500");
	}
	temp.focus();
}

</script>

<? // add_site_form_3_template.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");
printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("After selecting a template, you will have completed all of the steps required for making your site.  You can finish now or continue for more options.</div>");
printc("<br />");

printc("<div class=title>5. Choose a Template".helplink("template","help")."</div>");

printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("If you want more, less, or different sections from those in any template, chose one that is close and add/delete sections later.</div>");
printc("<br />");

include("templates/descriptions.inc");

printc ("<table width=100% cellspacing='0'>");
$i=0;

foreach ($_templates as $template=>$tname) {

	// Get the site object and detrmine if it is active.
	$templateSite =& new site($template);
	$templateSite->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,0,true);
	if ($templateSite->canView()) {
		printc("<tr>");
		printc("<td align='center' valign=top><a name='row$i'><input type=radio name=template value=$template".(($_SESSION[settings][template]==$template)?" checked":"")." onClick=\"selectRow('$i')\"></td>");
		printc("<td width=25% valign=top style='border-top: 1px solid #aaa;'><b>$tname</b></td>");
	
		printc("<td valign=top align='right' style='border-top: 1px solid #aaa;'><input type=button class='small' value='Preview This Template' onClick='doTemplateWin(\"$template\")'></td>");
		printc("</tr>\n");
		printc("<tr>");
		printc("<td> &nbsp; </td>");
		printc("<td valign=top colspan=2 style='padding-left: 20px; padding-right: 20px; padding-bottom: 15px;'>".(($descriptions[$template])?"$descriptions[$template]":"No description availible")."</td>");
		printc("</tr>");
		$i++;
	}
}
/*printc("<tr>");
	printc("<td valign=top><input type=radio name=template value=''".(($_SESSION[settings][template]=='')?" checked":"")."></td>");
	printc("<td valign=top style='border-top: 1px solid #aaa;'>none</td>");
	printc("<td valign=top style='border-top: 1px solid #aaa;'>This setting give you a completely blank site, useful for advaced users familliar with two-level navigation. If this is your first site with SiteDB, please select one of the other templates or the \"Default\" template.</td>");
	printc("<td valign=top style='border-top: 1px solid #aaa;'><input type=button class='small' value='Preview This Template' onClick='doTemplateWin(\" \")'></td>");
	printc("</tr>\n");
*/
printc("</table>\n");
printc("<br />");
printc("<br />");

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");

printc("</form>");
