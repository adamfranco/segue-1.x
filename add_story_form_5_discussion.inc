<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='$settings[site]'>");
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
//	if ($settings[type] == "page") printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


	if ($auser == $site_owner || db_get_value("pages","ediscussion","id=$settings[page]")==1) {
		printc("<div class=title>Discussion</div>");
		printc("<div class=desc>You have the option of allowing visitors to your site discuss certain content contained in it. You can restrict discussion access to students in the class (if this is a class website), only Middlebury users, or anyone (allowing for anonymous discussion).</div>");
		printc("<div class=leftmargin>");
		printc("Enable discussion for this story: <input type=checkbox value='1' name=discuss".(($settings[discuss])?" checked":"")."><br>");
		printc("Who should be able to discuss this story? <select name=discusspermissions>");
		printc("<option value='anyone'".(($settings[discusspermissions]=='anyone')?" selected":"")."> anyone");
		printc("<option value='midd'".(($settings[discusspermissions]=='midd')?" selected":"")."> Middlebury College users");
		if (isclass($settings[site])) printc("<option value='class'".(($settings[discusspermissions]=='class')?" selected":"").">students in class");
		printc("</select>");
		printc("</div>");
		printc("<br>");
	} else {
		printc("<input type=hidden name=discuss value=$settings[discuss]>");
		printc("<input type=hidden name=discusspermissions value='$settings[discusspermissions]'>");
	}
	
	printc("Category: <input type=text name=newcategory size=20 value='$settings[newcategory]'> <select name=category onChange='document.storyform.newcategory.value = document.storyform.category.value'>");
	printc("<option value=''".(($settings[category]=='')?" selected":"").">none");
	foreach ($settings[categories] as $c) {
		printc("<option value='$c'".(($settings[category]==$c)?" selected":"").">$c");
	}
	printc("</select>");

printc("<br><br>");


printc("</form>");

