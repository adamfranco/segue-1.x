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
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


if ($settings[type]=='story') {
	//	Begin modification 1 - 11.1.02 - achapin
	// removed active-x editor code to editor_activex.php
	// editor.inc.php determines browser/platform and choses appropriate editor
	//print $shorttext;
	include("htmleditor/editor.inc.php");
		
	printc("<br>");
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Extended Content (Optional):");
	printc("<br>");
		
	//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	printc(addeditor ("longertext",80,16,$settings[longertext]));
	printc("</td></tr></table>");

	printc("<div align=right>");
	printc("What type of text did you enter?");
//	printc (" <select name=texttype>");
//	printc("<option value=text".(($settings[texttype]=='text')?" selected":"").">Plain text");
//	printc("<option value=html".(($settings[texttype]=='html')?" selected":"").">Preformatted HTML");
//	printc("</select>");
	printc("<input type=textarea disabled value='".(($settings[texttype]=='text')?" Plain text":" Preformatted HTML")."'>");
	printc("</div>");
	printc("<div class=desc align=right>Change this option on the previous screen.</div>");
	//	end modification 1 - 11.1.02 - achapin
}

if ($settings[type] != 'story') printc("<br><br>");

printc("</form>");

