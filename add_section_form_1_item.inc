<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

//printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($settings[add]) printc("<div class=title>Add Section to site \"".db_get_value("sites","title","name='$settings[site]'")."\"</div>");
else  printc("<div class=title>Edit Item \"".db_get_value("sections","title","id=$settings[section]")."\" in Site \"".db_get_value("sites","title","name='$settings[site]'")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


printc("Type:".helplink("pagetypes"));

if ($settings[add]) printc("<div class=desc>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br><br>");


if ($settings[add] || $settings[type] == 'section') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='section'".(($settings[type]=='section')?" checked":"").">");
	printc(" Content Page</b>".(($settings[type]=='page')?" - allows you to create pages with content, such as class syllabi or a resume. (default)":"")."</div>");
}

if ($settings[add] || $settings[type] == 'url') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='url'".(($settings[type]=='url')?" checked":"").">");
	printc(" Link/URL</b>".(($settings[type]=='url')?" - allows you to enter an internet address (such as http://www.middlebury.edu/yourpage/) to allows users to click on to look at a different website. Links will open in a new window, allowing people to continue browsing your site.":"")."</div>");
}

if ($settings[type]=='url') {
	printc("<div class=title>URL</div>");
	printc("<div class=desc>Enter a title and URL for your link.</div>");
}
if ($settings[type]=='section') {
	printc("<div class=title>Content Section</div>");
	printc("<div class=desc>Enter a title for your page below.</div>");
}

printc("<div class=leftmargin>");
printc("Title: <input type=text size=35 name=title value='".spchars($settings[title])."'>");
printc("</div><br>");

if ($settings[type]=='url') {	
	printc("<div class=leftmargin>");
	printc("URL: <input type=text name=url value='$settings[url]' size=50>");
	printc("</div>");
}

printc("<br><br>");

printc("</form>");

