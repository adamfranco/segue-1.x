<? /* $Id$ */ ?>
<script lang="JavaScript">

function doHeaderFooterWin() {	
// This is not currently implemented. It is just an attempt at printing a preview.  
// I can't pass the header or footer to the output.
	var header = "<? print urlencode($_SESSION[siteObj]->getField("header")); ?>";
	var footer = "<? print urlencode($_SESSION[siteObj]->getField("footer")); ?>";
	var template = "<? print $_SESSION[settings][template]; ?>";
	var theme = "<? print $_SESSION[siteObj]->getField("theme"); ?>";
	var themesettings = "<? print $_SESSION[siteObj]->getField("themesettings"); ?>";
	if (template != '') {
		var temp = window.open("index.php?action=site&site="+template+"&siteheader="+header+"&footer="+footer+"&themesettings="+themesettings+"&theme="+theme,"template","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=500");
	}
	temp.focus();
}

</script>

<? // add_site_form_5_header.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");
printc("<input type=hidden name='step'>");

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

// --- title ---
printc("<div class=title>".(($_SESSION[settings][edit])?"6":"7").". Custom Header".helplink("headerfooter")." &nbsp; - &nbsp; <i>Optional</i></div>");

printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("A custom header can be an image or text. It will appear at the top of all pages in your site.  Some themes look better with headers than others.</div>");
printc("<br>");
printc("<br>");

// --- form contents ---
//printc ("<input type=button name=update value='Update Header' onClick=\"submitForm()\">");

/******************************************************************************
* Media Library: links to filebrowser.php and returns media urls
* for copy/paste into editor
* NOTE: this Media Library link is NOT the same as in the Image/File Block UI
******************************************************************************/

printc("<br><div class=leftmargin><table cellpadding=5 cellspacing=0 style='border: 1px solid gray'>");
printc("<tr><td colspan=2>");
if ($_SESSION[settings][edit]) {
	printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=html\")' target='filebrowser' style='text-decoration: none'>");
	printc("<input type=hidden name='mediaimg_url' value='".$_SESSION[settings][mediaimg_url]."'>");
	printc(" <input type=text name='media_url' class='textfield small' value='".$_SESSION[settings][media_url]."' size=75 readonly>");
}
// We can't add media if we don't have a site id yet
else {
	printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' style='text-decoration: none' disabled>");
	printc(" <input type=text name='media_url' class='textfield small' value='You must save the site before you can add media to it.' size=75 readonly>");
}

printc(" (media file URL)");
printc("</div></td></tr>");
printc("<tr><td>");
printc("<div style='font-size:10px; color:#003399'>1. Browse your Media Library for media files. <br>2. Copy the media file URL from field above and paste into link tool (files) or image tool (images)</div>");
printc("</td></tr>");
printc("</table></div><br><br>");

//if ($_SESSION[siteObj]->getField("footer")) printc("<input type=submit name=copyfooter value='Copy From Footer'>");
//printc("<br><br>");

//	print out text areas with editor (determined by addeditor function)
//  addeditor function (defined in editor.inc.php)
//  addeditor function variables (textarea, cols, rows, text)
require_once("htmleditor/editor.inc.php");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 ******************************************************************************/

$mod_header = convertTagsToInteralLinks($_SESSION[settings][sitename], $_SESSION[siteObj]->getField("header"));

printc(addeditor("header",60,8,$mod_header));

//printc(addeditor("header",60,8,$_SESSION[siteObj]->getField("header")));

//printc("<textarea name=header cols=70 rows=8>");
//$temp = spchars($_SESSION[settings][header]);
//printc("$temp");
//printc("</textarea>");
printc("<br>");
printc("<br>");

printc("<div class=title>Current Header:");
//printc(" <input type=button class='small' value='Preview The Site' onClick='doHeaderFooterWin()'>"); //see notes in script above
printc("</div>");
$header = stripslashes($_SESSION[siteObj]->getField("header"));
printc("$header");
printc("<br>");
printc("<br>");

if ($_SESSION[siteObj]->getField("footer") != "") {
	printc("<div class=title>Current Footer:</div>");
	$footer = stripslashes($_SESSION[siteObj]->getField("footer"));
	printc("$footer");
	printc("<br>");
	printc("<br>");
}

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

printc("</form>");
