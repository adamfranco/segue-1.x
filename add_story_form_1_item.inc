<? /* $Id$ */

printc("<form action='index.php?$sid&action=add_story' method=post name='addform'>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
/* 	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n"); */
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");

//printc($_REQUEST[shorttext]);
//printc($_REQUEST[title]);

/******************************************************************************
 * Content Type Radio Buttons
 ******************************************************************************/

printc("Type:".helplink("storytypes"));

if ($_SESSION[settings][add]) printc("<div class=desc>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br><br>");


if ($_SESSION[settings][add] || $_SESSION[storyObj]->getField("type") == 'story') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='story'".(($_SESSION[storyObj]->getField("type")=='story')?" checked":"").">");
	printc(" Text Block</b>".(($_SESSION[storyObj]->getField("type")=='story')?" - allows you to enter a block of content on this page.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[storyObj]->getField("type") == 'image') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='image'".(($_SESSION[storyObj]->getField("type")=='image')?" checked":"").">");
	printc(" Image</b>".(($_SESSION[storyObj]->getField("type")=='image')?" - displays an image of your choice on the page with optional caption or description text.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[storyObj]->getField("type") == 'file') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='file'".(($_SESSION[storyObj]->getField("type")=='file')?" checked":"").">");
	printc(" File for Download</b>".(($_SESSION[storyObj]->getField("type")=='file')?" - displays a download link to a file of your choice, such as a Word document containing syllabus information.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[storyObj]->getField("type") == 'link') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='link'".(($_SESSION[storyObj]->getField("type")=='link')?" checked":"").">");
	printc(" Link/URL</b>".(($_SESSION[storyObj]->getField("type")=='link')?" - displays a link with optional title and description.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[storyObj]->getField("type") == 'rss') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='rss'".(($_SESSION[storyObj]->getField("type")=='rss')?" checked":"").">");
	printc(" RSS</b>".(($_SESSION[storyObj]->getField("type")=='rss')?" - displays an RSS feed.":"")."</div>");
}

/******************************************************************************
 * Content Type instructions
 ******************************************************************************/
   
if ($_SESSION[storyObj]->getField("type")=='image') {
	printc("<div class=title>Image - ".helplink("image")."</div>");
	printc("<div class=desc>Select an image file to use, and enter optional title and caption text.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='file') {
	printc("<div class=title>File for Download</div>");
	printc("<div class=desc>Select the file you would like to make available, and enter optional title and description text.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='link') {
	printc("<div class=title>Link</div>");
	printc("<div class=desc>Enter the URL (internet address) to the page you would like to link to below, and enter optional title and description.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='rss') {
	printc("<div class=title>RSS Feed</div>");
	printc("<div class=desc>Enter the URL (internet address) of the desired RSS feed.</div>");
}
if ($_SESSION[storyObj]->getField("type")=='story') {
	printc("<div class=title>Text Block</div>");
	printc("<div class=desc>Enter the content for your story below. Title and full content are optional. If you enter full content on the next screen, the shorter content (or Abstract) will be displayed on your page with a link to the full content, in a new window.</div>");
}


/******************************************************************************
 * Title Field: optional for text, image, link; required for file;
 * not used for rss
 ******************************************************************************/

//printc("<div class=leftmargin>");
printc("<br><table cellpadding=5 cellspacing=0 style='border: 1px solid gray'>");


if ($_SESSION[storyObj]->getField("type")!='rss') {
	printc("<tr>");
	printc("<td>Title:</td><td><input type=text size=50 name=title value='".spchars($_SESSION[storyObj]->getField("title"))."'>");
	if ($_SESSION[storyObj]->getField("type") == 'file') {
		printc(" <span style='font-size:10px; color: #D00'> *required</span>");
	} else {
		printc(" <span style='font-size:10px; color: #gray'> (optional)</span>");
	}
	printc("</td></tr>");
		
} else if ($_SESSION[storyObj]->getField("type")=='rss') {	
	printc("<tr>");
	printc("<td>URL:</td><td><input type=text name=url value='".$_SESSION[storyObj]->getField("url")."' size=75></td>");
	printc("</tr>");
	
}

if ($_SESSION[storyObj]->getField("type")=='link') {
	printc("<tr>");
	printc("<td>URL:<td><input type=text name=url value='".$_SESSION[storyObj]->getField("url")."' size=75></td>");

}

printc("</td></tr>");
printc("</table>");


/******************************************************************************
 * Short Text: link = description; story = abstract;
 ******************************************************************************/

if ($_SESSION[storyObj]->getField("type")=='link') {	
	printc("<br><br>Description (optional):");
	printc("<br>");
	printc("<textarea name=shorttext class=textarea rows=4 cols=80>".spchars($_SESSION[storyObj]->getField("shorttext"))."</textarea>");
}


/******************************************************************************
 * Text Block: includes HTML Editor code and link to Media Library
 * 
 ******************************************************************************/

if ($_SESSION[storyObj]->getField("type")=='story') {
	
	/******************************************************************************
 	* Media Library: links to filebrowser.php and returns media urls
 	* for copy/paste into editor
 	* NOTE: this Media Library link is NOT the same as in the Image/File Block UI
 	******************************************************************************/
	
	printc("<br><div class=leftmargin><table cellpadding=5 cellspacing=0 style='border: 1px solid gray'>");
	printc("<tr><td colspan=2>");
	printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=html\")' target='filebrowser' style='text-decoration: none'>");
	printc("<input type=hidden name='mediaimg_url' value='".$_SESSION[settings][mediaimg_url]."'>");
	printc(" <input type=text name='media_url' class='textfield small' value='".$_SESSION[settings][media_url]."' size=75 readonly>");
	printc(" (media file URL)");
	printc("</div></td></tr>");
	printc("<tr><td>");
	printc("<div style='font-size:10px; color:#003399'>1. Browse your Media Library for media files. <br>2. Copy the media file URL from field above and paste into link tool (files) or image tool (images)</div>");
	printc("</td></tr>");
	printc("</table></div>");
	
	/******************************************************************************
	 * Internal linking: note to users...
	 ******************************************************************************/
	
	printc("<table width= 80% cellpadding=3 cellspacing=0>");
	printc("<tr><td>");
	printc("<div style='font-size:10px; color:#D00'>");
	printc("Note: Links to other pages in this site must use the address displayed in the PREVIEW site mode not the EDIT mode. ");
	printc("To ensure correct link URL's to any page on this site, click Preview this Site button before copying URL.<br>");
	printc("</div>");
	printc("</td></tr>");
	printc("</table>");

	/******************************************************************************
	 * 	Sniff determines browser and os
	 *  (sniffer included in editor.inc.php...)
	 ******************************************************************************/
	include("sniffer.inc.php");
	
	//$uagent = $_SERVER["HTTP_USER_AGENT"];
	//printc("<div style='font-size:10px; color:#999'>($uagent)</div>");

	/******************************************************************************
	 * HTML Editor: check for supported browsers
	 * browser recommendations note
	 ******************************************************************************/

	if ($supported == 0) {
		printc("<br><div style='font-size:10px;'>Looks like you are using a browser that does not support a built-in HTML editor.  ");
		if ($isWin) {
			printc("We recommend you use either <a href=http://www.mozilla.org/download.html target=new_window>Mozilla 1.3 or greater</a> or
			<a href=http://www.microsoft.com/windows/ie/ target=new_window>Internet Explorer or greater</a>.");
		} else {
			printc("We recommend you use <a href=http://www.mozilla.org/download.html target=new_window>Mozilla 1.3 or greater</a>.");
		}		
		//printc("<div style='font-size:10px; color:#999'>browser information: $uagent</div>");		
		printc("  In the meantime, if you are editing existing content that is in HTML format, you will need to work
		around the html tags.</div>");
	}
	
	if ($supported == 1) {
		if (!$_SESSION[settings][editor]) $_SESSION[settings][editor] = 'htmlarea';
		//printpre($_SESSION[settings][editor]);
		if ($isWinIE) {
			printc("<div style='font-size:10px;'>Your web browser supports two different HTML editors.  ");
			printc("We recommend you chose HTMLarea for the greatest cross-platform compatibility.</div>");
			printc("<div align=right>Current Editor: <select name=editor onChange='document.addform.submit()'>");
			printc("<option value=htmlarea".(($_SESSION[settings][editor]=='htmlarea')?" selected":"").">HTMLarea");
			printc("<option value=activex".(($_SESSION[settings][editor]=='activex')?" selected":"").">Active-X");
			printc("</select></div>");
			//printc("<input type=button value='Update' onClick='document.addform.submit()'>\n");						
		}	
	}
	
	/******************************************************************************
 	* Include HTML Editor code
	 ******************************************************************************/
	
	include("htmleditor/editor.inc.php");
	
	/******************************************************************************
	 * 	print out text areas with editor (determined by addeditor function)
	 *  addeditor function (defined in editor.inc.php)
	 *  addeditor function variables (textarea, cols, rows, text)
	 ******************************************************************************/
		
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Content (or Abstract):");
	printc("<br>");
			
	if ($_SESSION[storyObj]->getField("texttype")=='text' && $supported == 1) {
		$text = htmlbr($_SESSION[storyObj]->getField("shorttext"));
		$_SESSION[settings][editorinitstate] = "text";
		//$_SESSION[storyObj]->getField("texttype") = "html";		
	} else {
		$text = $_SESSION[storyObj]->getField("shorttext");
	}
	
	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 ******************************************************************************/
	$specfic_mediapath = $cfg[uploadurl]."/".$_SESSION[settings][site];
	$general_mediapath = "mediapath";
	$text = eregi_replace($general_mediapath, $specfic_mediapath, $text);

	printc(addeditor ("shorttext",60,20,$text));
	printc("</td></tr></table>");
	
	/******************************************************************************
	 * Text fields text type: html or text
	 ******************************************************************************/
		
	if ($supported == 1) {
		printc("<input type=hidden name='texttype' value='html'>");
	} else {
		printc("<div align=right>");
		printc("What type of text did you enter? <select name=texttype>");
		printc("<option value=text".(($_SESSION[storyObj]->getField("texttype")=='text')?" selected":"").">Plain text");
		printc("<option value=html".(($_SESSION[storyObj]->getField("texttype")=='html')?" selected":"").">Preformatted HTML");
		printc("</select>");
		printc("</div>");
		printc("<div class=desc style>If you choose \"Preformatted HTML\" Segue will not convert newline characters to the HTML equivalent: &lt;br&gt;. If you are unsure, select <b>Plain text</b>.</div>");
	}
	//	end modification 1 - 11.1.02 - achapin
}

/******************************************************************************
 * File/Image Block: Media Library Upload UI
 ******************************************************************************/

if ($_SESSION[storyObj]->getField("type") == 'image' || $_SESSION[storyObj]->getField("type") == 'file') {

	printc("<br><div class=leftmargin><table cellpadding=5 cellspacing=0 style='border: 1px solid gray'>");
	printc("<tr><td colspan=2>");
	printc('<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'">');
	printc("<input type=hidden name='libraryfileid' value='".$_SESSION[settings][libraryfileid]."'>");
	printc("<input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=none\")' target='filebrowser' style='text-decoration: none'>");	
	printc(" <input type=text class='textfield' small name='libraryfilename' value='".$_SESSION[settings][libraryfilename]."' size=75 readonly>");
	//if ($_SESSION[storyObj]->getField("type")=='image')

	//printc("<div style='font-size:10px; color:#gray'><input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=html\")' target='filebrowser' style='text-decoration: none'>");
	//printc("<input type=hidden name='mediaimg_url' value='".$_SESSION[settings][mediaimg_url]."'>");
	//printc(" <input type=text name='media_url' class='textfield small' value='".$_SESSION[settings][media_url]."' size=75 readonly>");
	//printc(" (media file URL)");
	printc("</td></tr>");
	printc("<tr><td>");
	if ($_SESSION[storyObj]->getField("type")=='image') {	
		printc("<div style='font-size:10px; color:#003399'>1. Browse your Media Library for image files. <br>");
 		printc("2. Accepted types here GIF, JPEG (JPG), and PNG. (chose <b>File for Download</b> to create links to other types of files)</div>");
 	} else {
		printc("<div style='font-size:10px; color:#003399'>1. Browse your Media Library for media files. <br>");
 		printc("(2. All File types accepted. (chose <b>Image</b> to display images inline)</div>"); 	
 	}
	printc("</td></tr>");
	printc("</table></div><br><br>");

	//printc("<br><br>");
	//printc('<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'">');
	//printc("<input type=hidden name='libraryfileid' value='".$_SESSION[settings][libraryfileid]."'>");
	//printc((($_SESSION[storyObj]->getField("type")=='image')?"Image":"File").": <input type=text name='libraryfilename' value='".$_SESSION[settings][libraryfilename]."' size=25 readonly> <input type=button name='browsefiles' value='Media Library' onClick='sendWindow(\"filebrowser\",700,600,\"filebrowser.php?&editor=none\")' target='filebrowser' style='text-decoration: none'> <br><br>");
	//if ($_SESSION[storyObj]->getField("type")=='image') printc("<div class=desc>Accepted image types are GIF, JPEG (JPG), and PNG.</div>");
	//else // printc("<div class=desc>Select the file you would like to make available by clicking the 'Browse...' button above.</div>");
	//printc("<br>");
	
	/******************************************************************************
	 * File/Image Block: Caption/Description 
	 ******************************************************************************/

	if ($_SESSION[storyObj]->getField("type")=='image') {
		printc("Caption/Description (optional):");
	} else {
		printc("Description (optional):");
	}
	
	printc("<br>");
	printc("<textarea name=shorttext class=textarea rows=4 cols=80>".spchars($_SESSION[storyObj]->getField("shorttext"))."</textarea>");
	
}

 printc("<br>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
/* 	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n"); */
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");