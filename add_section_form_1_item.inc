<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method='post' name='addform'>");
printc("<input type='hidden' name='step'>");

//printc("<input type='hidden' name='site' value='".$thisSite->name."'>");
//printc("<input type='hidden' name='section' value='$settings[section]'>");
//printc("<input type='hidden' name='page' value='$settings[page]'>");
//if ($edit) printc("<input type='hidden' name='edit_page' value='$settings[page]'>");
printc("<input type='hidden' name='action' value='$action'>");

//printc("<input type='hidden' name='mode' value='$mode'>");
printc("<input type='hidden' name='typeswitch' value='0'>");

if ($_SESSION[settings][add]) printc("<div class='title'>Add ".(($_SESSION[sectionObj]->getField("type") == 'section')?"Section":"Link")." to site \"".$thisSite->getField("title")."\"</div>");
else  printc("<div class='title'>Edit Item \"".$_SESSION[sectionObj]->getField("title")."\" in Site \"".$thisSite->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
	printc("<input type='button' value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width='50%' align='right'>");
	printc("<input type='button' value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type='submit' name='save' value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel'>");
printc("<input type='hidden' value='0' name='prevbutton'>");
printc("<input type='hidden' value='0' name='nextbutton'>");


printc("Type:".helplink("pagetypes"));

/******************************************************************************
 * Content Type Radio Buttons
 ******************************************************************************/


if ($_SESSION[settings][add]) printc("<div class='desc'>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br /><br />");


if ($_SESSION[settings][add] || $_SESSION[sectionObj]->getField("type") == 'section') {
	printc("<div class='desc'><b>");
	printc("<input type='radio' name='type' ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='section'".(($_SESSION[sectionObj]->getField("type")=='section')?" checked='checked'":"").">");
	printc(" Content Section</b>".(($_SESSION[sectionObj]->getField("type")=='section')?" - allows you to create a section in which links and pages with content can be created. (default)":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[sectionObj]->getField("type") == 'link') {
	printc("<div class='desc'><b>");
	printc("<input type='radio' name='type' ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='link'".(($_SESSION[sectionObj]->getField("type")=='link')?" checked='checked'":"").">");
	printc(" Link/URL</b>".(($_SESSION[sectionObj]->getField("type")=='link')?" - allows you to enter an internet address (such as http://www.middlebury.edu/yourpage/) to allows users to click on to look at a different website. Links will open in a new window, allowing people to continue browsing your site.":"")."</div>");
}

/******************************************************************************
 * Content Type instructions
 ******************************************************************************/

if ($_SESSION[sectionObj]->getField("type")=='link') {
	printc("<div class='title'>URL</div>");
	printc("<div class='desc'>Enter a title and URL for your link.</div>");
}
if ($_SESSION[sectionObj]->getField("type")=='section') {
	printc("<div class='title'>Content Section</div>");
	printc("<div class='desc'>Enter a title for your section below.</div>");
}

printc("<div class='leftmargin'>");
if (isset($_REQUEST["title"])) {	
	printc("Title: <input type='text' size='35' name='title' value='".spchars($_REQUEST['title'])."'>");
} else {
	printc("Title: <input type='text' size='35' name='title' value='".spchars($_SESSION[sectionObj]->getField("title"))."'>");
}
printc("</div><br />");

//if ($_SESSION[sectionObj]->getField("type")=='section') {
//	printc("<div class='title'>Display Options</div>");
//	printc("Hide Sidebar: <input type='checkbox' name='hide_sidebar' value='1'". (($_SESSION[sectionObj]->getField("hide_sidebar") == 1)?" checked='checked'":"") . "><br />");
//	printc("<div class='desc'>By checking the above box, the left sidebar and links to all pages in this sidebar will be hidden when viewing the site (the sidebar will NOT be hidden while editing this site).</div>");
//
//}


if ($_SESSION[sectionObj]->getField("type")=='link') {	

	$url = $_SESSION[sectionObj]->getField("url");

	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 * replace general with specific
	 ******************************************************************************/
	$url = convertTagsToInteralLinks($_SESSION[settings][site], $url);

	printc("<div class='leftmargin'>");
	printc("URL: <input type='text' name='url' value='".$url."' size='50'>");
	printc("</div>");
}

printc("<br /><br />");

printc("</form>");

