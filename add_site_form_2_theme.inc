
<script lang="JavaScript">

//var theme = document.addform.theme.value;

function submitForm() {
	document.addform.submit();	
}

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
	if (theme == "<? print $settings[theme]; ?>") {
		var themesettings = document.addform.themesettings.value;
	} else {
		var themesettings = "";
	}
	if (from == "settings") {
		doThemeSettings();	
	}
	var edit = <? print $settings[edit]; ?>;
	var sitename = "<? print $settings[sitename]; ?>";
	if (edit) {
		var preview = window.open("index.php?action=site&site="+sitename+"&themesettings="+themesettings+"&theme="+theme+"&themepreview=1","preview","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	} else {
		var preview = window.open("index.php?action=site&site=sample&themesettings="+themesettings+"&theme="+theme,"preview","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	}
//	document.addform.previewhandle.value = preview;
	preview.focus();
}

function doTemplateWin() {
	var template = document.addform.template.value;
	var theme = "<? print $settings[theme]; ?>";
	var themesettings = document.addform.themesettings.value;
	if (template != '') {
		var temp = window.open("index.php?action=site&site="+template+"&themesettings="+themesettings+"&theme="+theme,"template","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=700");
	}
	temp.focus();
}

function openThemeSettings() {
	theme = "<? print $settings[theme]; ?>";
	doPreviewWin(theme,"");
	doThemeSettings();
}

function doThemeSettings() {
	var f = document.addform;
	var theme = "<? print $settings[theme]; ?>";
	var themesettings = document.addform.themesettings.value;
	var site = "<? print $settings[sitename]; ?>";
	var themesettings = window.open("themesettings.php?<?echo $sid?>&theme="+theme+"&site="+site+"&themesettings="+themesettings+"&updatemethod=javascript","settings","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500");
//	document.addform.themesettingshandle.value = themesettings;
}

</script>

<? // add_site_form_2_theme.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($settings[add])?"add_site":"edit_site")."' method=post name=addform>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($settings[edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");
if ($settings[add]) printc("<div class=title>Add Site</div>");
if ($settings[edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

// --- title ---
printc("<div class=title>4. Appearance: &nbsp;Select a Theme".helplink("theme","help")."</div>");


// --- form contents ---
printc("<table width=100% cellspacing=0px>");
printc("<tr><td> </td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Click on image to preview</td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Name</td><td valign=bottom align=center style='border-bottom: 0px solid #aaa; color: #aaa;'>Select a theme to<br>edit its settings</td></tr>");

$i=0;
foreach ($possible_themes as $themename=>$longname) {
	printc("<tr><td align=center><a name='row$i'>");
	printc("<input type=radio name=theme value='$themename' ".(($settings[theme]==$themename)?" checked":"")." onClick=\"selectTheme('$i')\"></td>");
	printc("<td align=center style='border-top: 1px solid #aaa;".(($settings[theme]==$themename)?" background-color: #eee":"")."'><a href=JavaScript:doPreviewWin(\"$themename\")><img src=$themesdir/$themename/thumbnail.jpg border=0 title='Click here to preview this theme'></a></td>");
	printc("<td align=center valign=center style='border-top: 1px solid #aaa;".(($settings[theme]==$themename)?" background-color: #eee":"")."'>$longname<br><a href=JavaScript:doPreviewWin(\"$themename\")>(preview)</a></td>");
	if ($settings[theme]==$themename)
		printc("<td align=center valign=center style='border-top: 1px solid #aaa;".(($settings[theme]==$themename)?" background-color: #eee":"")."'><input type=button class='small' value='Edit Theme Settings' onClick='openThemeSettings()'></td>");
	else 
		printc("<td align=center valign=top style='border-top: 1px solid #aaa;'> &nbsp; </td>");
	printc("</tr>");
	$i++;
}

printc("<input type=hidden name=themesettings value='".$settings[themesettings]."'>");
printc("<input type=hidden name=themesettingshandle value='".$themesettingshandle."'>");
printc("<input type=hidden name=previewhandle value='".$previewhandle."'>");

printc("</table>");
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
	if ($settings[edit]) printc("<input type=submit name=save value='Save Changes'>\n");
printc("</td></tr></table>");

printc("</form>");