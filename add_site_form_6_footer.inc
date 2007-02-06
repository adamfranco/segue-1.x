<? /* $Id$ */ ?>
<? // add_site_form_6_footer.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");
printc("<input type=hidden name='step'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value=' &nbsp; &nbsp; Finish &nbsp; &nbsp; '>\n"); 
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
/* 	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n");  */
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");
printc("<br />");

// --- title ---
printc("<div class=title>".(($_SESSION[settings][edit])?"7":"8").". Custom Footer".helplink("headerfooter")." &nbsp; - &nbsp; <i>Optional</i></div>");
printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("A custom footer can be an image or text. It will appear at the bottom of all pages in your site.  Some themes look better with footers than others.</div>");
printc("<br />");
printc("<br />");

// --- form contents ---
//printc ("<input type=button name=update value='Update Footer' onClick=\"submitForm()\">");
/******************************************************************************
* Media Library: links to filebrowser.php and returns media urls
* for copy/paste into editor
* NOTE: this Media Library link is NOT the same as in the Image/File Block UI
******************************************************************************/

//printc("<br /><div class=leftmargin><table cellpadding=5 cellspacing='0' style='border: 1px solid gray'>");
//printc("<tr><td colspan=2>");
if ($_SESSION[settings][edit]) {
//	printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=html\")' target='filebrowser' style='text-decoration: none'>");
//	printc("<input type=hidden name='mediaimg_url' value='".$_SESSION[settings][mediaimg_url]."'>");
//	printc(" <input type='text' name='media_url' class='textfield small' value='".$_SESSION[settings][media_url]."' size=75 readonly>");
}
// We can't add media if we don't have a site id yet
else {
	printc("If you want to add an image to your footer, you must save this site first.  Once saved, you can return here by choosing to edit site settings.</ br></ br>");
//	printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' style='text-decoration: none' disabled>");
//	printc(" <input type='text' name='media_url' class='textfield small' value='You must save the site before you can add media to it.' size=75 readonly>");
}

/******************************************************************************
 * 	Sniff determines browser and os
 *  (sniffer included in editor.inc.php...)
 ******************************************************************************/
if ($_REQUEST['editor'])
	$_SESSION['html_editor'] = $_REQUEST['editor'];
	
include("sniffer.inc.php");

/******************************************************************************
 * HTML Editor: check for supported browsers
 * browser recommendations note
 ******************************************************************************/

if ($supported == 0) {
	printc("<br /><div style='font-size:10px;'>Looks like you are using a browser that does not support a built-in HTML editor.  ");
	if ($isWin) {
		printc("We recommend you use either <a href='http:'//www.mozilla.org/download.html target='new_window'>Mozilla 1.3 or greater</a> or
		<a href='http:'//www.microsoft.com/windows/ie/ target='new_window'>Internet Explorer or greater</a>.");
	} else {
		printc("We recommend you use <a href='http:'//www.mozilla.org/download.html target='new_window'>Mozilla 1.3 or greater</a>.");
	}		
	//printc("<div style='font-size:10px; color:#999'>browser information: $uagent</div>");		
	printc("  In the meantime, if you are editing existing content that is in HTML format, you will need to work
	around the html tags.</div>");
}
if ($supported == 1) {
	if (!$_SESSION['html_editor']) $_SESSION['html_editor'] = 'fckeditor';
	//printpre($_SESSION['html_editor']);
	if ($isWinIE) {
		printc("<div style='font-size:10px;'>Your web browser supports three different HTML editors.  ");
		printc("We recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
		printc("<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
		printc("<option value='fckeditor'".(($_SESSION['html_editor']=='fckeditor')?" selected='selected'":"").">FCKeditor");
		printc("<option value='htmlarea'".(($_SESSION['html_editor']=='htmlarea')?" selected='selected'":"").">HTMLarea");
		printc("<option value='activex'".(($_SESSION['html_editor']=='activex')?" selected='selected'":"").">Active-X");
		printc("\n<option value='txt'".(($_SESSION['html_editor']=='txt')?" selected='selected'":"").">None</option>");
		printc("</select></div>");
		//printc("<input type='button' value='Update' onclick='document.addform.submit()' />\n");						
	} else {
		printc("<div style='font-size:10px;'>Your web browser supports two different HTML editors.  ");
		printc("We recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
		printc("<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
		printc("<option value='fckeditor'".(($_SESSION['html_editor']=='fckeditor')?" selected='selected'":"").">FCKeditor");
		printc("\n<option value='txt'".(($_SESSION['html_editor']=='txt')?" selected='selected'":"").">None</option>");
		printc("</select></div>");		
	}
}

//	print out text areas with editor (determined by addeditor function)
//  addeditor function (defined in editor.inc.php)
//  addeditor function variables (textarea, cols, rows, text)
require_once("htmleditor/editor.inc.php");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 ******************************************************************************/
$mod_footer = convertTagsToInteralLinks($_SESSION[settings][sitename], $_SESSION[siteObj]->getField("footer"));

if ($_SESSION[settings][edit]) {
	printc(addeditor("footer",100,8,$mod_footer));
} else {
	printc(addeditor("footer",100,8,$mod_footer,"initsite"));
}

//printc(addeditor("footer",60,8,$_SESSION[siteObj]->getField("footer")));


printc("<br />");
printc("<br />");

if ($_SESSION[siteObj]->getField("header") != "") {
	printc("<div class=title>Current Header:</div>");
	$header = stripslashes($_SESSION[siteObj]->getField("header"));
	printc("$header");
	printc("<br />");
	printc("<br />");
}

printc("<div class=title>Current Footer:</div>");
$footer = stripslashes($_SESSION[siteObj]->getField("footer"));
printc("$footer");
printc("<br />");
printc("<br />");


printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value=' &nbsp; &nbsp; Finish &nbsp; &nbsp; '>\n"); 
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
/* 	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n");  */
printc("</td></tr></table>");

printc("</form>");
