<? /* $Id$ */ ?>

<script lang="JavaScript">

//var theme = document.addform.theme.value;

function selectTheme(row) {
	document.addform.themesettings.value = "";
	if (row > 1) {
		row = row - 1;
		action = document.addform.action;
		document.addform.action=action+"#row"+row;
	}
	document.addform.submit();
}

function closeAll() {
	var preview = document.addform.previewhandle.value;
	var themsettings = document.addform.themsettingshandle.value;
	preview.window.close();
	themesettings.window.close();
}

function doPreviewWin(theme, from) {
	if (theme == "<? print $_SESSION[siteObj]->getField('theme'); ?>") {
		var themesettings = document.addform.themesettings.value;
	} else {
		var themesettings = "";
	}
	if (from == "settings") {
		doThemeSettings();	
	}
	var edit = <? print $_SESSION[settings][edit]; ?>;
	var sitename = "<? print $_SESSION[siteObj]->getField("name"); ?>";
	if (edit) {
		var preview = window.open("index.php?action=site&site="+sitename+"&themesettings="+themesettings+"&theme="+theme+"&themepreview=1&nostatus=1","preview","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	} else {
		var preview = window.open("index.php?action=site&site=sample&nostatus=1&themesettings="+themesettings+"&theme="+theme,"preview","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	}
//	document.addform.previewhandle.value = preview;
//	preview.focus();
}

function doTemplateWin() {
	var template = document.addform.template.value;
	var theme = "<? print $_SESSION[siteObj]->getField("theme"); ?>";
	var themesettings = document.addform.themesettings.value;
	if (template != '') {
		var temp = window.open("index.php?action=site&site="+template+"&themesettings="+themesettings+"&theme="+theme,"template","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	}
	temp.focus();
}

function openThemeSettings() {
	theme = "<? print $_SESSION[siteObj]->getField("theme"); ?>";
	var prev = doPreviewWin(theme,"");
	// timeout is in place to prevent the themesettings window from getting hidden.
	var them = setTimeout("doThemeSettings();",2000);
}

function doThemeSettings() {
	var f = document.addform;
	var theme = "<? print $_SESSION[siteObj]->getField("theme"); ?>";
	var themesettings = document.addform.themesettings.value;
	var site = "<? print $_SESSION[siteObj]->getField("name"); ?>";
	var settings = window.open("themesettings.php?<?echo $sid?>&theme="+theme+"&site="+site+"&themesettings="+themesettings+"&updatemethod=javascript","settings","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500");
//	document.addform.themesettingshandle.value = themesettings;
	settings.focus();
}

</script>

<? // add_site_form_2_theme.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");
printc("<input type=hidden name='step'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

printc("<div class=desc>Set your browser to allow pop ups from this page in order to properly edit Segue themes.</div>");
// --- title ---
printc("<div class=title>4. Appearance: &nbsp;Select a Theme".helplink("theme","help")."</div>");


// --- form contents ---
printc("<table width=100% cellspacing=0px>");
printc("<tr><td> </td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Click on image to preview</td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Name</td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Select a theme to<br>edit its settings</td></tr>");

$i=0;
foreach ($possible_themes as $themename=>$longname) {
	printc("<tr><td align=center><a name='row$i'>");
	printc("<input type=radio name=theme value='$themename' ".(($_SESSION[siteObj]->getField("theme")==$themename)?" checked":"")." onClick=\"selectTheme('$i')\"></td>");
	printc("<td align=center style='border-top: 1px solid #aaa;".(($_SESSION[siteObj]->getField("theme")==$themename)?" background-color: #eee":"")."'><a href=JavaScript:doPreviewWin(\"$themename\")><img src=$themesdir/$themename/thumbnail.jpg border=0 title='Click here to preview this theme'></a></td>");
	printc("<td align=center valign=center style='border-top: 1px solid #aaa;".(($_SESSION[siteObj]->getField("theme")==$themename)?" background-color: #eee":"")."'>$longname<br><a href=JavaScript:doPreviewWin(\"$themename\")>(preview)</a></td>");
	if ($_SESSION[siteObj]->getField("theme")==$themename)
		printc("<td align=center valign=center style='border-top: 1px solid #aaa;".(($_SESSION[siteObj]->getField("theme")==$themename)?" background-color: #eee":"")."'><input type=button class='small' value='Edit Theme Settings' onClick='openThemeSettings()'><div style='font-size: 9px;'>(disable popup window blocker)".helplink("theme","?")."</div></td>");
	else 
		printc("<td align=center valign=top style='border-top: 1px solid #aaa;'> &nbsp; </td>");
	printc("</tr>");
	$i++;
}

printc("<input type=hidden name=themesettings value='".$_SESSION[siteObj]->getField("themesettings")."'>");
printc("<input type=hidden name=themesettingshandle value='".$themesettingshandle."'>");
printc("<input type=hidden name=previewhandle value='".$previewhandle."'>");

printc("</table>");
printc("<br>");
printc("<br>");

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");

printc("</form>");