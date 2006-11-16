<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method='post' name='addform'>");
printc("<input type='hidden' name='step' />");

//printc("<input type='hidden' name='site' value='$_SESSION[settings][site]' />");
//printc("<input type='hidden' name='section' value='$_SESSION[settings][section]' />");
//printc("<input type='hidden' name='page' value='$_SESSION[settings][page]' />");
//if ($edit) printc("<input type='hidden' name='edit_page' value='$_SESSION[settings][page]' />");
printc("<input type='hidden' name='action' value='$action' />");

//printc("<input type='hidden' name='mode' value='$mode' />");
printc("<input type='hidden' name='typeswitch' value='0' />");

if ($_SESSION[settings][add]) printc("<div class='title'>Add Item to Section \"".$thisSection->getField("title")."\"</div>");
else printc("<div class='title'>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
	printc("<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("</td><td width='50%' align='right'>");
/* 	printc("<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n"); */
	if ($_SESSION[pageObj]->getField("type") == 'page' || $_SESSION[pageObj]->getField("type") == 'link') printc("<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type='submit' name='save' value='Save Now' />\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel' />");
printc("<input type='hidden' value='0' name='prevbutton' />");
printc("<input type='hidden' value='0' name='nextbutton' />");


printc("<div class='title'>Item Type:".helplink("pagetypes")."</div>");

/******************************************************************************
 * Content Type Select
 ******************************************************************************/

if ($_SESSION[settings][add]) {
	//printc("<div class='desc'>Choose a type of page.</div>");
} else {
	printc("<br /><br />");
}

if ($_SESSION[settings][add]) {
	printc("Choose an item type: ");
	printc("<select name='type' onchange='typeChange()'>");
	printc("<option value='page'".(($_SESSION[pageObj]->getField("type")=='page')?" selected='selected'":"").">New Page</option>");
	printc("<option value='content'".(($_SESSION[pageObj]->getField("type")=='content')?" selected='selected'":"").">Sidebar Content Block</option>");
	printc("<option value='rss'".(($_SESSION[pageObj]->getField("type")=='rss')?" selected='selected'":"").">RSS Feed</option>");
	printc("<option value='tags'".(($_SESSION[pageObj]->getField("type")=='tags')?" selected='selected'":"").">Categories</option>");
	printc("<option value='link'".(($_SESSION[pageObj]->getField("type")=='link')?" selected='selected'":"").">Link/URL</option>");
	printc("<option value='heading'".(($_SESSION[pageObj]->getField("type")=='heading')?" selected='selected'":"").">Heading</option>");
	printc("<option value='divider'".(($_SESSION[pageObj]->getField("type")=='divider')?" selected='selected'":"").">Divider</option>");
	printc("<option value='participants'".(($_SESSION[pageObj]->getField("type")=='participants')?" selected='selected'":"").">Participant List</option>");
	printc("</select><br /><br />");
}

if ($_SESSION[pageObj]->getField("type") == 'page') {
	printc("<div class='desc'>A Page is a sidebar link to a location where you can add any number of content blocks.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'content') {
	printc("<div class='desc'>Sidebar Content is a single block of content in the left or right sidebar of this section of your site.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'rss') {
	printc("<div class='desc'>An RSS feed displays titles from any RSS feed in the left or right column of this section of your site.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'tags') {
	printc("<div class='desc'>Categories will list all the categories you have assigned to content on this site with links to all content for any given category.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'participants') {
	printc("<div class='desc'>Participants is a list of all the participants in this site.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'link') {
	printc("<div class='desc'>A link/URL is a link to any page within this site or outside of this site (like <i>http://www.middlebury.edu/</i>).</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'heading') {
	printc("<div class='desc'>Headings can be used to organize content in the left or right column of this section of your site.</div>");
} else if ($_SESSION[pageObj]->getField("type") == 'divider') {
	printc("<div class='desc'>A divider will put some sort of dividing space between items in your navigation list.</div>");
}

/******************************************************************************
 * Content Type instructions
 ******************************************************************************/

if ($_SESSION[pageObj]->getField("type")=='link') {
	printc("<div class='desc'>Enter a title and URL for your link.</div>");
} else if ($_SESSION[pageObj]->getField("type")=='rss') {
	printc("<div class='desc'>Enter the URL of your RSS feed.</div>");
} else if ($_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class='desc'>Enter a title for your page below.</div>");
} else if ($_SESSION[pageObj]->getField("type")=='content') {
	printc("<div class='desc'>Enter content and title below.  Then specify which sidebar to display this content</div>");
}


printc("<table cellpadding='5' cellspacing='0'>");

/******************************************************************************
 * Publication Site Options
 ******************************************************************************/

/*   enter author's name for publication site */
if ($thisSite->getField("type") == 'publication') {
	printc("<div class='leftmargin'>");
	printc("Author: <input type='text' name='url' value='".$_SESSION[pageObj]->getField("url")."' size='25' />");
	$authors = $thisSite->getAllValues("page","url");
	$nauthors = array();
	foreach ($authors as $auth) {
		if (ereg("http",$auth)) continue;
		$parts = explode(" ",$auth);
		$nauthors[$auth] = (ereg(",",$auth))?$parts[0]:$parts[1];
	}
	asort($nauthors);
	$list = '';
	foreach (array_keys($nauthors) as $auth) {
		$list .= "<option>$auth\n";
	}
	if ($list != '') {
		printc(" <select name='authortemp' onchange='document.addform.url.value = document.addform.authortemp.value;'>");
		printc("<option value=''>Choose...\n");
		printc($list);
		printc("</select>");
	}
	printc("</div>");

/******************************************************************************
 * Page options
 ******************************************************************************/
	
} else if ($_SESSION[pageObj]->getField("type")=='page') {	
	printc("<tr><td>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."' /></td></tr>");

/******************************************************************************
 * Heading options
 ******************************************************************************/

} else if ($_SESSION[pageObj]->getField("type")=='heading') {	
	printc("<tr><td>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."' /></td></tr>");
	
/******************************************************************************
 * Link options
 * includes: title, url
 * requires: url
 ******************************************************************************/

} else if ($_SESSION[pageObj]->getField("type")=='link') {	

	$url = $_SESSION[pageObj]->getField("url");
	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 * replace general with specific
	 ******************************************************************************/
	$url = convertTagsToInteralLinks($_SESSION[settings][site], $url);
	
	// replace internal links in edit mode (action=viewsite)
	// with internal links in non-edit mode (action=site)

	printc("<tr><td align='right'>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."' /></td></tr>");
	printc("<tr><td align='right'>URL:</td><td><input type='text' name='url' value='".$url."' size='50' /></td></tr>");

/******************************************************************************
 * RSS options
 * includes: title, RSS feed url
 * requires: RSS feed url
 ******************************************************************************/
	
} else if ($_SESSION[pageObj]->getField("type")=='rss') {
	$url = $_SESSION[pageObj]->getField("url");
	$url = convertTagsToInteralLinks($_SESSION[settings][site], $url);
	
	if ($_SESSION[pageObj]->getField("archiveby") == "none") {
		$rss_display = 5;
	} else {
		$rss_display = $_SESSION[pageObj]->getField("archiveby");	
	}
	printc("<tr><td align='right'>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."' />");	printc("</td></tr>");
	printc("<tr><td align='right'>URL:</td><td><input type='text' name='url' value='".$url."' size='50' /></td></tr>");
	printc("<tr><td align='right'>Maximum Entries:</td><td><input type='text' name='archiveby' value='".$rss_display."' size='5' /></td></tr>");	

/******************************************************************************
 * Categories options
 * includes: title
 * requires: title
 ******************************************************************************/
	
} else if ($_SESSION[pageObj]->getField("type")=='tags') {
//	printc("<tr><td align='right'>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."'>");	printc("</td></tr>");

/******************************************************************************
 * Participant options
 * includes: title
 * requires: title
 ******************************************************************************/
	
} else if ($_SESSION[pageObj]->getField("type")=='participants') {
//	printc("<tr><td align='right'>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."'>");	printc("</td></tr>");

/******************************************************************************
 * Content options
 * includes: title, content
 * requires: content
 ******************************************************************************/
	
} else if ($_SESSION[pageObj]->getField("type")=='content') {
	printc("<tr><td align='right'>Title:</td><td><input type='text' size='50' name='title' value='".spchars($_SESSION[pageObj]->getField("title"))."' /></td></tr>");
	printc("<tr><td colspan='2'>");
	printc("<table width='70%' cellpadding='3' cellspacing='0' style='border: 1px solid grey'>");
	printc("<tr><td>");
	printc("Content:".helplink('msword'));
	printc("</td></tr>");
	printc("<tr><td>");

	/******************************************************************************
	 * 	Sniff determines browser and os
	 *  (sniffer included in editor.inc.php...)
	 ******************************************************************************/
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
		if (!$_SESSION[settings][editor]) $_SESSION[settings][editor] = 'fckeditor';
		//printpre($_SESSION[settings][editor]);
		if ($isWinIE) {
			printc("<div style='font-size:10px;'>Your web browser supports three different HTML editors.  ");
			printc("We recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
			printc("<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
			printc("<option value='fckeditor'".(($_SESSION[settings][editor]=='fckeditor')?" selected='selected'":"").">FCKeditor");
			printc("<option value='htmlarea'".(($_SESSION[settings][editor]=='htmlarea')?" selected='selected'":"").">HTMLarea");
			printc("<option value='activex'".(($_SESSION[settings][editor]=='activex')?" selected='selected'":"").">Active-X");
			printc("</select></div>");
			//printc("<input type='button' value='Update' onclick='document.addform.submit()' />\n");						
		} else {
			printc("<div style='font-size:10px;'>Your web browser supports two different HTML editors.  ");
			printc("We recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
			printc("<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
			printc("<option value='fckeditor'".(($_SESSION[settings][editor]=='fckeditor')?" selected='selected'":"").">FCKeditor");
			printc("<option value='htmlarea'".(($_SESSION[settings][editor]=='htmlarea')?" selected='selected'":"").">HTMLarea");
			printc("</select></div>");		
		}
	}
	
	/******************************************************************************
 	* Include HTML Editor code
	 ******************************************************************************/
	
	require_once("htmleditor/editor.inc.php");
	
	/******************************************************************************
	 * 	print out text areas with editor (determined by addeditor function)
	 *  addeditor function (defined in editor.inc.php)
	 *  addeditor function variables (textarea, cols, rows, text)
	 ******************************************************************************/
		
	printc("<table width='100%'><tr><td style='padding-left: 15px'>");
	
	//printc("<br />");
			
	$text = $_SESSION[pageObj]->getField("text");
	
	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 * replace general with specific
	 ******************************************************************************/
	$text = convertTagsToInteralLinks($_SESSION[settings][site], $text);
	
	// If they had been editing as plain-text, but are now using the editor,
	// convert the line-breaks to <br /> tags.
	
//	if ($_SESSION[pageObj]->getField("texttype")=='text' && $supported)
//		$text = htmlbr($text);

	// replace internal links in edit mode (action=viewsite)
	// with internal links in non-edit mode (action=site)
	//$action_viewsite = "action=viewsite";
	//$action_site = "action=site";			
	//$text = eregi_replace($action_viewsite, $action_site, $text);

	printc(addeditor ("text",60,20,$text, "page", "html"));
	//printc(addeditor ("text",60,20,$text, "page", "html"));
	printc("</td></tr></table>");
	
	/******************************************************************************
	 * Text fields text type: html or text
	 ******************************************************************************/
		
	if ($supported == 1) {
		printc("<input type='hidden' name='texttype' value='html' />");
	} else {
		printc("<div align='right'>");
		printc("What type of text did you enter? <select name='texttype'>");
		//printc("<option value='text'".(($_SESSION[pageObj]->getField("texttype")=='text')?" selected='selected'":"").">Plain text");
		//printc("<option value='html'".(($_SESSION[pageObj]->getField("texttype")=='html')?" selected='selected'":"").">Preformatted HTML");
		printc("</select>");
		printc("</div>");
		printc("<div class='desc' style>If you choose \"Preformatted HTML\" Segue will not convert newline characters to the HTML equivalent: &lt;br&gt;. If you are unsure, select <b>Plain text</b>.</div>");
	}
	
	printc("</td></tr>");
	printc("</table>");
	printc("</td></tr>");
}
printc("</table>");
//printc("<br /><br />");


printc("<div class='title'>Item Location</div>");
printc("<div class='desc'>Chose which sidebar (left or right) to display this item.</div>");
printc("<table width = '95%' cellspacing='3' cellpadding='3'>");
printc("<tr><td  style='border: 1px  solid; background-color: #cccccc;' colspan='3' align='center'><br /></td></tr>");
printc("<tr><td width='25%' align='center' style='border: 1px solid; background-color: #cccccc;'>Left<br /><input type='radio' value='left' name='location'".(($_SESSION[pageObj]->getField("location") == 'left' || !$_SESSION[pageObj]->getField("location"))?" checked='checked'":"")." /></td>");
printc("<td width='50%' align='center' style='border: 1px solid; background-color: #cccccc;'>&nbsp;</td>");
printc("<td width='25%' style='border: 1px solid; background-color: #cccccc;' align='center'>Right<br /><input type='radio' value='right' name='location'".(($_SESSION[pageObj]->getField("location") == 'right')?" checked='checked'":"")." /></td></tr>");
printc("<tr><td style='border: 1px  solid; background-color: #cccccc;' colspan='3' align='center'><br /></td></tr>");
printc("</table>");

printc("</form>");

