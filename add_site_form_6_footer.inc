<? /* $Id$ */ ?>
<? // add_site_form_6_footer.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($_SESSION[settings][add])?"add_site":"edit_site")."' method=post name=addform>");
printc("<input type=hidden name='step'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value=' &nbsp; &nbsp; Finish &nbsp; &nbsp; '>\n"); 
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
/* 	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n");  */
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[settings][add]) printc("<div class=title>Add Site</div>");
if ($_SESSION[settings][edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

// --- title ---
printc("<div class=title>".(($_SESSION[settings][edit])?"7":"8").". Custom Footer".helplink("headerfooter")." &nbsp; - &nbsp; <i>Optional</i></div>");
printc ("<div style='font-size: 10px; margin-left: 30px; margin-right: 30px;'>");
printc("A custom footer can be an image or text. It will appear at the bottom of all pages in your site.  Some themes look better with footers than others.</div>");
printc("<br>");
printc("<br>");

// --- form contents ---
printc ("<input type=button name=update value='Update Footer' onClick=\"submitForm()\">");
if ($_SESSION[siteObj]->getField("header")) printc("<input type=submit name=copyheader value='Copy From Header'>");
printc("<br>");

//	print out text areas with editor (determined by addeditor function)
//  addeditor function (defined in editor.inc.php)
//  addeditor function variables (textarea, cols, rows, text)
include("htmleditor/editor.inc.php");
printc(addeditor("footer",60,8,$_SESSION[siteObj]->getField("footer")));


printc("<br>");
printc("<br>");

if ($_SESSION[siteObj]->getField("header") != "") {
	printc("<div class=title>Current Header:</div>");
	$header = stripslashes($_SESSION[siteObj]->getField("header"));
	printc("$header");
	printc("<br>");
	printc("<br>");
}

printc("<div class=title>Current Footer:</div>");
$footer = stripslashes($_SESSION[siteObj]->getField("footer"));
printc("$footer");
printc("<br>");
printc("<br>");


printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
/* 	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n"); */
	if ($_SESSION[settings][add]) printc("<input type=submit name=save value=' &nbsp; &nbsp; Finish &nbsp; &nbsp; '>\n"); 
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($_SESSION[settings][edit]) printc("<input type=submit name=save value='Save Changes'>\n");
/* 	if ($_SESSION[settings][add]) printc("<input type=submit name=save value='Finish Now'>\n");  */
printc("</td></tr></table>");

printc("</form>");
