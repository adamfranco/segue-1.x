<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

//printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($settings[add]) printc("<div class=title>Add Content to Page \"".db_get_value("pages","title","id=$settings[page]")."\" in Section \"".db_get_value("sections","title","id=$settings[section]")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".db_get_value("pages","title","id=$settings[page]")."\" in Section \"".db_get_value("sections","title","id=$settings[section]")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


printc("Type:".helplink("storytypes"));

if ($settings[add]) printc("<div class=desc>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br><br>");


if ($settings[add] || $settings[type] == 'story') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='story'".(($settings[type]=='story')?" checked":"").">");
	printc(" Text Block</b>".(($settings[type]=='story')?" - allows you to enter a block of content on this page.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'image') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='image'".(($settings[type]=='image')?" checked":"").">");
	printc(" Image</b>".(($settings[type]=='image')?" - displays an image of your choice on the page with optional caption or description text.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'file') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='file'".(($settings[type]=='file')?" checked":"").">");
	printc(" File for Download</b>".(($settings[type]=='file')?" - displays a download link to a file of your choice, such as a Word document containing syllabus information.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'link') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='link'".(($settings[type]=='link')?" checked":"").">");
	printc(" Link/URL</b>".(($settings[type]=='link')?" - displays a link with optional title and description.":"")."</div>");
}


if ($settings[type]=='image') {
	printc("<div class=title>Image</div>");
	printc("<div class=desc>Select an image file to use, and enter optional title and caption text.</div>");
}
if ($settings[type]=='file') {
	printc("<div class=title>File for Download</div>");
	printc("<div class=desc>Select the file you would like to make available, and enter optional title and description text.</div>");
}
if ($settings[type]=='link') {
	printc("<div class=title>Link</div>");
	printc("<div class=desc>Enter the URL (internet address) to the page you would like to link to below, and enter optional title and description.</div>");
}
if ($settings[type]=='story') {
	printc("<div class=title>Text Block</div>");
	printc("<div class=desc>Enter the content for your story below. Title and full content are optional. If you enter full content on the next screen, the shorter content (or Abstract) will be displayed on your page with a link to the full content, in a new window.</div>");
}



//if ($settings[type] !='divider') {
	printc("<div class=leftmargin>");
	printc("Title: <input type=text size=35 name=title value='".spchars($settings[title])."'>");
	printc("<br></div>");
//}


if ($settings[type]=='link') {	
	printc("<div class=leftmargin>");
	printc("URL: <input type=text name=url value='$settings[url]' size=50>");
	printc("</div>");
	printc("Description (optional):");
	printc("<br>");
	printc("<textarea name=shorttext class=textarea rows=4 cols=80>".spchars($settings[shorttext])."</textarea>");
}

if ($settings[type]=='story') {
	//	Begin modification 1 - 11.1.02 - achapin
	// removed active-x editor code to editor_activex.php
	// editor.inc.php determines browser/platform and choses appropriate editor
	//print $shorttext;
	include("htmleditor/editor.inc.php");
		
	printc("<br>");
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Content (or Abstract):");
	printc("<br>");
		
	//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	printc(addeditor ("shorttext",80,16,$settings[shorttext]));
	printc("</td></tr></table>");

	printc("<div align=right>");
	printc("What type of text did you enter? <select name=texttype>");
	printc("<option value=text".(($settings[texttype]=='text')?" selected":"").">Plain text");
	printc("<option value=html".(($settings[texttype]=='html')?" selected":"").">Preformatted HTML");
	printc("</select>");
	printc("</div>");
	printc("<div class=desc>Choose above if the text you entered is formatted HTML (the formatting commands used by the WWW). If you are unsure, select <b>Plain text</b>.</div>");
	printc("<div class=desc><b>NOTE</b>: <b>Plain text</b> may still contain HTML commands. By choosing <b>Preformatted HTML</b>, SitesDB will not convert newline characters to the HTML equivalent: &lt;br&gt;</div>");
	//	end modification 1 - 11.1.02 - achapin
}


if ($settings[type] == 'image' || $settings[type] == 'file') {
	printc("<br><br>");
	printc('<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'">');
	if ($settings[edit]) printc("New ");
	if ($settings[type] == 'image') printc("Image: ");
	else printc("File: ");
	printc("<input type=file name=file value='$settings[file] class=textfield>");
	if ($settings[type]=='image') printc("<div class=desc>Select the image you would like to use by clicking the 'Browse...' button above. Accepted image types are GIF, JPEG and PNG.</div>");
	else printc("<div class=desc>Select the file you would like to make available by clicking the 'Browse...' button above.</div>");
	printc("<br>");
	if ($settings[type]=='image') printc("Caption/Description (optional):");
	else printc("Description (optional):");
	printc("<br>");
	printc("<textarea name=shorttext class=textarea rows=4 cols=80>".spchars($settings[shorttext])."</textarea>");
	
}

if ($settings[type] != 'story') printc("<br><br>");

printc("</form>");

