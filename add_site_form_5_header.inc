
<script lang="JavaScript">
//var theme = document.addform.theme.value;

function submitForm() {
	document.addform.submit();	
}

function doHeaderFooterWin() {	
// This is not currently implemented. It is just an attempt at printing a preview.  
// I can't pass the header or footer to the output.
	var header = "<? print urlencode($settings[header]); ?>";
	var footer = "<? print urlencode($settings[footer]); ?>";
	var template = "<? print $settings[template]; ?>";
	var theme = "<? print $settings[theme]; ?>";
	var themesettings = "<? print $settings[themesettings]; ?>";
	if (template != '') {
		var temp = window.open("index.php?action=site&site="+template+"&siteheader="+header+"&footer="+footer+"&themesettings="+themesettings+"&theme="+theme,"template","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=500");
	}
	temp.focus();
}

</script>

<? // add_site_form_5_header.inc -- the form that people use to add/edit a site

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
	if ($settings[add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");
if ($settings[add]) printc("<div class=title>Add Site</div>");
if ($settings[edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

// --- title ---
printc("<div class=title>".(($settings[edit])?"6":"7").". Custom Header".helplink("headerfooter")." &nbsp; - &nbsp; <i>Optional</i></div>");

printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("A custom header can be an image or text. It will appear at the top of all pages in your site.  Some themes look better with headers than others.</div>");
printc("<br>");
printc("<br>");

// --- form contents ---
printc ("<input type=button name=update value='Update Header' onClick=\"submitForm()\">");
if ($settings[footer]) printc("<input type=submit name=copyfooter value='Copy From Footer'>");
printc("<br>");

//	print out text areas with editor (determined by addeditor function)
//  addeditor function (defined in editor.inc.php)
//  addeditor function variables (textarea, cols, rows, text)
include("htmleditor/editor.inc.php");
printc(addeditor ("header",60,8,$text));

//printc("<textarea name=header cols=70 rows=8>");
//$temp = spchars($settings[header]);
//printc("$temp");
//printc("</textarea>");
printc("<br>");
printc("<br>");

printc("<div class=title>Current Header:");
//printc(" <input type=button class='small' value='Preview The Site' onClick='doHeaderFooterWin()'>"); //see notes in script above
printc("</div>");
printc("$settings[header]");
printc("<br>");
printc("<br>");

if ($settings[footer] != "") {
	printc("<div class=title>Current Footer:</div>");
	printc("$settings[footer]");
	printc("<br>");
	printc("<br>");
}

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

printc("</form>");
