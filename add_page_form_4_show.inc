<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($settings[add]) printc("<div class=title>Add Item to Section \"".db_get_value("sections","title","id=$settings[section]")."\"</div>");
else  printc("<div class=title>Edit Item \"".db_get_value("pages","title","id=$settings[page]")."\" in Section \"".db_get_value("sections","title","id=$settings[section]")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	//printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


if ($settings[type]=='page') {
	printc("<div class=title style='font-size: 14px'>Display Options</div>");
	printc("<div class=leftmargin>");
	
	printc("Order the Content Blocks on this page by: ");
	printc("<select name='storyorder'>");
		printc("<option value=''".(($settings[storyorder]=='')?" selected":"").">Custom (Default)");
		printc("<option value='addeddesc'".(($settings[storyorder]=='addeddesc')?" selected":"").">Recent First");
		printc("<option value='addedasc'".(($settings[storyorder]=='addedasc')?" selected":"").">Recent Last");
		printc("<option value='editeddesc'".(($settings[storyorder]=='editeddesc')?" selected":"").">Recently Edited First");
		printc("<option value='editedasc'".(($settings[storyorder]=='editedasc')?" selected":"").">Recently Edited Last");
		printc("<option value='author'".(($settings[storyorder]=='author')?" selected":"").">Author");
		printc("<option value='editor'".(($settings[storyorder]=='editor')?" selected":"").">Editor");
		printc("<option value='category'".(($settings[storyorder]=='category')?" selected":"").">Category");
	printc("</select><br><br>");
		
	printc("Show added/edited by <i>username</i> for content: <input type=checkbox name='showcreator' value=1". (($settings[showcreator])?" checked":"") . "><BR>");
	printc("<div class=desc>By checking the above box, the usernames of the people that added or edited the content on this page will be displayed along with the story text.</div>");
		
	printc("Show added/edited timestamp: <input type=checkbox name='showdate' value=1". (($settings[showdate])?" checked":"") . "><br>");
	printc("<div class=desc>By checking the above box, the date and time content was added (and edited, if that be the case), will be displayed along with the story text.</div>");
	
	printc("Archive content on this site by: ");
	printc("<select name=archiveby>");
	$l = array("none"=>"Do not archive","week"=>"Week","month"=>"Month","year"=>"Year");
	foreach ($l as $n=>$v) {
		printc("<option value='$n'".(($settings[archiveby]==$n)?" selected":"").">$v\n");
	}
	printc("</select><br>");
	printc("<div class=desc>You can organize the content in this site by week added, month added, or year added. This useful for journal-like pages, where content is long and added often.</div>");
			
	printc("</div>");
}

printc("<br><br>");


printc("</form>");

