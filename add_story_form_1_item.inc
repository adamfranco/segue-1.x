<? /* $Id$ */

printc("\n<form action='index.php?$sid&amp;action=add_story' method='post' name='addform'>");
printc("\n<input type='hidden' name='step' />");

//printc("\n<input type='hidden' name='site' value='$_SESSION[settings][site]' />");
//printc("\n<input type='hidden' name='section' value='$_SESSION[settings][section]' />");
//printc("\n<input type='hidden' name='page' value='$_SESSION[settings][page]' />");
//if ($edit) printc("\n<input type='hidden' name='edit_page' value='$_SESSION[settings][page]' />");
printc("\n<input type='hidden' name='action' value='$action' />");

//printc("\n<input type='hidden' name='mode' value='$mode' />");
printc("\n<input type='hidden' name='typeswitch' value='0' />");

if ($_SESSION[settings][add]) printc("\n<div class='title'>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("\n<div class='title'>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("\n<table width='100%'><tr><td width='50%'>");
	printc("\n<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("\n</td><td width='50%' align='right'>");
/* 	printc("\n<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n"); */
	printc("\n<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n");
printc("\n</td></tr><tr><td>");
printc("\n</td><td align='right'>");
	printc("\n<input type='submit' name='save' value='Save Now' />\n");
printc("\n</td></tr></table>");
printc("\n<input type='hidden' value='0' name='cancel' />");
printc("\n<input type='hidden' value='0' name='prevbutton' />");
printc("\n<input type='hidden' value='0' name='nextbutton' />");

//printc($_REQUEST[shorttext]);
//printc($_REQUEST[title]);

/******************************************************************************
 * Content Type Select
 ******************************************************************************/

printc("\n<div class='title'>Content Type:".helplink("storytypes")."</div>");

if ($_SESSION[settings][add]) {
//	printc("\n<div class='desc'>Please choose what type of content you would like to add. The choices are:</div>");
} else {
	printc("\n<br /><br />");
}

if ($_SESSION[settings][add]) {
	printc("\nChoose a content type: ");
	printc("\n<select name='type' onchange='typeChange()'>");
	printc("\n<option value='story'".(($_SESSION[storyObj]->getField("type")=='story')?" selected='selected'":"").">New Content</option>");
	printc("\n<option value='file'".(($_SESSION[storyObj]->getField("type")=='file')?" selected='selected'":"").">File for Download</option>");
	printc("\n<option value='link'".(($_SESSION[storyObj]->getField("type")=='link')?" selected='selected'":"").">Link/URL</option>");
	printc("\n<option value='rss'".(($_SESSION[storyObj]->getField("type")=='rss')?" selected='selected'":"").">RSS Feed</option>");
	printc("\n<option value='image'".(($_SESSION[storyObj]->getField("type")=='image')?" selected='selected'":"").">Image</option>");
	printc("\n</select><br \><br \>");
}


if ($_SESSION[storyObj]->getField("type") == 'story') {
	printc("\n<div class='desc'>A content block allows you to enter any kind of text or HTML.  Images can be included (see the Media Library below)</div>");
} else if ($_SESSION[storyObj]->getField("type") == 'image') {
	printc("\n<div class='desc'>An image from your Media Libary with optional caption or description text.  (you can also include images in content blocks)</div>");
} else if ($_SESSION[storyObj]->getField("type") == 'file') {
	printc("\n<div class='desc'> File for download creates a link to any file from your Media Library.</div>");
} else if ($_SESSION[storyObj]->getField("type") == 'link') {
	printc("\n<div class='desc'>Link/URL displays a link with optional title and description. (links can be included in content blocks an well</div>");
} else if ($_SESSION[storyObj]->getField("type") == 'rss') {
	printc("\n<div class='desc'>An RSS feeds will display the titles and descriptions of items from any RSS feed.</div>");
}

/******************************************************************************
 * Content Type instructions
 ******************************************************************************/
   
if ($_SESSION[storyObj]->getField("type")=='image') {
	printc("\n<div class='desc'>Browse for image file to use.  Title and caption text are optional.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='file') {
	printc("\n<div class='desc'>Browse for any file.  Title is required, description text is optional.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='link') {
	printc("\n<div class='desc'>Enter a URL (internet address).  Title and description are optional.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='rss') {
	printc("\n<div class='desc'>Enter a URL (internet address) of an RSS feed below.  As, well specify the number of items to display</div>");
}
if ($_SESSION[storyObj]->getField("type")=='story') {
	printc("\n<div class='desc'>Enter content in text area below. Title and extended content are optional. If you enter extended content (see next screen), the shorter content (or Abstract) will be displayed on your page with a link to the full content, in a new window.</div>");
}


/******************************************************************************
 * Title Field: optional for text, image, link; required for file;
 * not used for rss
 ******************************************************************************/
printc("\n<br /><table cellpadding='5' cellspacing='0' style='border: 1px solid gray'>");


if ($_SESSION[storyObj]->getField("type")!='rss') {
	printc("\n<tr>");
	
	if ($_SESSION[storyObj]->getField("type") =='image') {
		printc("\n<td>Caption:</td>");
	} else {
		printc("\n<td>Title:</td>");
	}
	
	if (isset($_REQUEST["title"])) {	
		printc("\n<td><input type='text' size='50' name='title' value='".spchars($_REQUEST["title"])."' />");
	} else {
		printc("\n<td><input type='text' size='50' name='title' value='".spchars($_SESSION[storyObj]->getField("title"))."' />");
	}
	
	if ($_SESSION[storyObj]->getField("type") == 'file') {
		printc("\n <span style='font-size:10px; color: #D00'> *required</span>");
	} else {
		printc("\n <span style='font-size:10px; color: #gray'> (optional)</span>");
	}
	printc("\n</td></tr>");
		
} else if ($_SESSION[storyObj]->getField("type")=='rss') {	
	if (!$_SESSION[storyObj]->getField("shorttext")) $_SESSION[storyObj]->setField("shorttext", 10);
	if (!$_SESSION[storyObj]->getField("longertext")) $_SESSION[storyObj]->setField("longertext", 0);
	printc("\n<tr>");
	printc("\n<td>URL:</td><td><input type='text' name='url' value='".$_SESSION[storyObj]->getField("url")."' size='75' /></td>");
	printc("\n</tr>");
	printc("\n<tr>");
	printc("\n<td>Number of items:</td><td><input type='text' name='shorttext' value='".$_SESSION[storyObj]->getField("shorttext")."' size='5' /> (standard view)</td>");
	printc("\n</tr>");
	printc("\n<tr>");
	printc("\n<td></td><td><input type='text' name='longertext' value='".$_SESSION[storyObj]->getField("longertext")."' size='5' /> (detail view)</td>");
	printc("\n</tr>");
	
}

if ($_SESSION[storyObj]->getField("type")=='link') {

	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 * replace general with specific
	 ******************************************************************************/
	 
	$url = $_SESSION[storyObj]->getField("url");
	
	$url = convertTagsToInteralLinks($_SESSION[settings][site], $url);


	printc("\n<tr>");
	printc("\n<td>URL:<td><input type='text' name='url' value='".$url."' size='75' /></td>");

}

printc("\n</td></tr>");
printc("\n</table>");

/******************************************************************************
 * Include option for versioning if type is new content
******************************************************************************/
// printc("<br />\n<table cellpadding='5' cellspacing='0' style='border: 1px solid gray'>");
// printc("\n<tr>");
// printc("\n<td><input type='checkbox' name='versioning' value='1' ");
// printc(($_SESSION[storyObj]->getField("versioning")=='1')?" checked":"");
// if ($_SESSION[auser] != $thisSite->owningSiteObj->owner) {
// 	printc(" disabled");
// }
// printc(">\n</td>");
// printc("\n<td>Show Versions: Check this to display a link to previous versions of this content block. ");
// printc("<i>(Only participants with permission to edit this content block will be able to revert to previous versions)</i>");
// printc("\n</td>");
// 
// printc("\n</td></tr>");
// printc("\n</table>");

/******************************************************************************
 * Determine the editor and if not set, set to default (i.e. fckeditor)
 ******************************************************************************/

if (!$_SESSION['html_editor']) $_SESSION['html_editor'] = 'fckeditor';


if ($_SESSION[storyObj]->getField("type")=='story' && $_SESSION['html_editor'] != 'fckeditor') {
	
	/******************************************************************************
 	* Media Library: links to filebrowser.php and returns media urls
 	* for copy/paste into editor
 	* NOTE: this Media Library link is NOT the same as in the Image/File Block UI
 	******************************************************************************/
	
	printc("\n<br /><div class='leftmargin'><table cellpadding='5' cellspacing='0' style='border: 1px solid gray'>");
	printc("\n<tr><td colspan='2'>");
	printc("\n<div style='font-size:10px; color:#gray'><input type='button' name='browsefiles' value='Media Library' onclick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&amp;editor=html\")' target='filebrowser' style='text-decoration: none' />");
	printc("\n<input type='hidden' name='mediaimg_url' value='".$_SESSION[settings][mediaimg_url]."' />");
	printc("\n <input type='text' name='media_url' class='textfield small' value='".$_SESSION[settings][media_url]."' size='75' readonly='readonly' />");
	printc("\n (media file URL)");
	printc("\n</div></td></tr>");
	printc("\n<tr><td>");
	printc("\n<div style='font-size:10px; color:#003399'>1. Browse your Media Library for media files. <br />2. Copy the media file URL from field above and paste into link tool (files) or image tool (images)</div>");
	printc("\n</td></tr>");
	printc("\n</table></div>");
}

/******************************************************************************
 * File/Image Block: Media Library Upload UI
 ******************************************************************************/

if ($_SESSION[storyObj]->getField("type") == 'image' || $_SESSION[storyObj]->getField("type") == 'file') {

	printc("\n<br /><div class='leftmargin'><table cellpadding='5' cellspacing='0' style='border: 1px solid gray'>");
	printc("\n<tr><td colspan='2'>");
	printc("\n<input type='hidden' name='libraryfileid' value='".$_SESSION[settings][libraryfileid]."' />");
	printc("\n<input type='button' name='browsefiles' value='Media Library' onclick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&amp;editor=none\")' target='filebrowser' style='text-decoration: none' />");	
	printc("\n <input type='text' class='textfield' small name='libraryfilename' value='".$_SESSION[settings][libraryfilename]."' size='75' readonly='readonly' />");

	printc("\n</td></tr>");
	printc("\n<tr><td>");
	if ($_SESSION[storyObj]->getField("type")=='image') {	
		printc("\n<div style='font-size:10px; color:#003399'>1. Browse your Media Library for image files. <br />");
 		printc("\n2. Accepted types here GIF, JPEG (JPG), and PNG. (chose <b>File for Download</b> to create links to other types of files)</div>");
 	} else {
		printc("\n<div style='font-size:10px; color:#003399'>1. Browse your Media Library for media files. <br />");
 		printc("\n(2. All File types accepted. (chose <b>Image</b> to display images inline)</div>"); 	
 	}
	printc("\n</td></tr>");
	printc("\n</table></div>");

}


/******************************************************************************
 * HTML Area
 ******************************************************************************/
if ($_SESSION[storyObj]->getField("type") != 'rss') {
	/******************************************************************************
	 * Internal linking: note to users...
	 ******************************************************************************/
	printc ("\n<br />");
	printc("\n<table width='100%' cellpadding='3' cellspacing='0' style='border: 1px solid grey'>");
	printc("\n<tr><td>");
	if ($_SESSION[storyObj]->getField("type")=='story') {
		printc("\nContent (or Abstract):".helplink('msword'));
	} else if ($_SESSION[storyObj]->getField("type")=='image') {
		printc("\nDescription (optional):");
	} else {
		printc("\nDescription (optional):");
	}
	printc("\n</td></tr>");
	printc("\n<tr><td>");

	/******************************************************************************
	 * 	Sniff determines browser and os
	 *  (sniffer included in editor.inc.php...)
	 ******************************************************************************/
	include("sniffer.inc.php");
	
	//$uagent = $_SERVER["HTTP_USER_AGENT"];
	//printc("\n<div style='font-size:10px; color:#999'>($uagent)</div>");

	/******************************************************************************
	 * HTML Editor: check for supported browsers
	 * browser recommendations note
	 ******************************************************************************/

	if ($supported == 0) {
		printc("\n<br /><div style='font-size:10px;'>Looks like you are using a browser that does not support a built-in HTML editor.  ");
		if ($isWin) {
			printc("\nWe recommend you use either <a href='http://www.mozilla.org/download.html' target='new_window'>Mozilla 1.3 or greater</a> or
			<a href='http://www.microsoft.com/windows/ie/' target='new_window'>Internet Explorer or greater</a>.");
		} else {
			printc("\nWe recommend you use <a href='http://www.mozilla.org/download.html' target='new_window'>Mozilla 1.3 or greater</a>.");
		}		
		//printc("\n<div style='font-size:10px; color:#999'>browser information: $uagent</div>");		
		printc("\n  In the meantime, if you are editing existing content that is in HTML format, you will need to work
		around the html tags.</div>");
	}
	
	if ($supported == 1) {
		
		//printpre($_SESSION['html_editor']);
		if ($isWinIE) {
			printc("\n<div style='font-size:10px;'>Your web browser supports three different HTML editors.  ");
			printc("\nWe recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
			printc("\n<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
			printc("\n<option value='fckeditor'".(($_SESSION['html_editor']=='fckeditor')?" selected='selected'":"").">FCKeditor</option>");
			printc("\n<option value='htmlarea'".(($_SESSION['html_editor']=='htmlarea')?" selected='selected'":"").">HTMLarea</option>");
			printc("\n<option value='activex'".(($_SESSION['html_editor']=='activex')?" selected='selected'":"").">Active-X</option>");
			printc("\n</select></div>");
			printc("\n<option value='txt'".(($_SESSION['html_editor']=='txt')?" selected='selected'":"").">None</option>");
			//printc("\n<input type='button' value='Update' onclick='document.addform.submit()' />\n");						
		} else {
			printc("\n<div style='font-size:10px;'>Your web browser supports two different HTML editors.  ");
			printc("\nWe recommend you chose FCKeditor for the greatest cross-platform compatibility.</div>");
			printc("\n<div align='right'>Current Editor: <select name='editor' onchange='document.addform.submit()'>");
			printc("\n<option value='fckeditor'".(($_SESSION['html_editor']=='fckeditor')?" selected='selected'":"").">FCKeditor</option>");
			printc("\n<option value='txt'".(($_SESSION['html_editor']=='txt')?" selected='selected'":"").">None</option>");
	//		printc("\n<option value='htmlarea'".(($_SESSION['html_editor']=='htmlarea')?" selected='selected'":"").">HTMLarea");
			printc("\n</select></div>");		
		}
	}
	
	/******************************************************************************
	 * Wiki Links instruction
	 ******************************************************************************/
	printc("\n<table width='90%' align='center' cellpadding='5' cellspacing='0' style='border: solid  1px;'><tr><td valign='top'>");
	printc("\nWiki linking".helplink('wiki').":</td>");
	printc("\n<td><div style='font-size:11px;'>To link to a page on your site whose title is \"Introduction\"");
	printc(" use [[introduction]]. If no content with the title \"Introduction\" exists a link to create such content will be made.  To see all titles used in this site, see: ");
	printc("<a href='site_map.php?site=".$_SESSION[settings][site]."&amp;show=wiki' onclick='window.open(\"\",\"Site Map\",\"height=500,width=400,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,resizable=yes,copyhistory=no\");' target='Site Map'>Site Map</a> </div>");	
	printc("\n</td></tr></table>");
	/******************************************************************************
 	* Include HTML Editor code
	 ******************************************************************************/
	
	require_once("htmleditor/editor.inc.php");
	
	/******************************************************************************
	 * 	print out text areas with editor (determined by addeditor function)
	 *  addeditor function (defined in editor.inc.php)
	 *  addeditor function variables (textarea, cols, rows, text)
	 ******************************************************************************/
		
	printc("\n<table width='100%'><tr><td style='padding-left: 5px'>");
	
	//printc("\n<br />");
			
	$text = $_SESSION[storyObj]->getField("shorttext");
	
	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 * replace general with specific
	 ******************************************************************************/
	$text = convertTagsToInteralLinks($_SESSION[settings][site], $text);
	
	// If they had been editing as plain-text, but are now using the editor,
	// convert the line-breaks to <br /> tags.
	if ($_SESSION[storyObj]->getField("texttype")=='text' && $supported)
		$text = htmlbr($text);
		
	// replace internal links in edit mode (action=viewsite)
	// with internal links in non-edit mode (action=site)
	//$action_viewsite = "action=viewsite";
	//$action_site = "action=site";			
	//$text = eregi_replace($action_viewsite, $action_site, $text);
	//printpre($_REQUEST);

	printc(addeditor ("shorttext",100,20,$text, "story", $_SESSION[storyObj]->getField("texttype")));
	printc("\n</td></tr></table>");
	
	/******************************************************************************
	 * Text fields text type: html or text
	 ******************************************************************************/
		
	if ($supported == 1 && $_SESSION['html_editor'] != 'txt') {
		printc("\n<input type='hidden' name='texttype' value='html' />");
	} else {
		printc("\n<div align='right'>");
		printc("\nWhat type of text did you enter? <select name='texttype'>");
		printc("\n<option value='text'".(($_SESSION[storyObj]->getField("texttype")=='text')?" selected='selected'":"").">Plain text</option>");
		printc("\n<option value='html'".(($_SESSION[storyObj]->getField("texttype")=='html')?" selected='selected'":"").">Preformatted HTML</option>");
		printc("\n</select>");
		printc("\n</div>");
		printc("\n<div class='desc' style>If you choose \"Preformatted HTML\" Segue will not convert newline characters to the HTML equivalent: &lt;br&gt;. If you are unsure, select <b>Plain text</b>.</div>");
	}
	
	printc("\n</td></tr>");
	
	/******************************************************************************
	 * Version comment field
	 ******************************************************************************/

	printc("\n<tr><td>");
	printc("\nComments about changes (optional):".helplink('versions'));
	printc("<br /><textarea name='version_comments' rows='5' cols='100'>");
	if ($_SESSION[storyObj]->version_comments) {
		printc($_SESSION[storyObj]->version_comments);
	} else if ($_SESSION[settings][add]) {
		printc("Initial version.");
	} else {
		printc("a minor edit...");
	}
	printc("</textarea>");
	printc("\n</td></tr>");	

}

 printc("\n<br />");

//----Cancel/Save/Next/Previous Buttons -----
printc("\n<table width='100%'><tr><td width='50%'>");
	printc("\n<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("\n</td><td width='50%' align='right'>");
/* 	printc("\n<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n"); */
	printc("\n<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n");
printc("\n</td></tr><tr><td>");
printc("\n</td><td align='right'>");
	printc("\n<input type='submit' name='save' value='Save Now' />\n");
printc("\n</td></tr></table>");
