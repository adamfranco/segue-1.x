<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

printc("<div class=title>Add Item to Section <b>".db_get_value("sections","title","id=$settings[section]")."</b></div>");


if ($settings[type]=='page') {
	printc("<div class=leftmargin>");
		
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

//printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid&action=viewsite&site=$site&section=$section'>");
printc("<input type=submit value='&lt;&lt Back' name=goback>\n");
printc("<input type=submit value='");
if ($settings[add])printc("Add");
if ($settings[edit])printc("Update");
printc("'>");

printc("</form>");

