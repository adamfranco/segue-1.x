<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

//printc("<input type=hidden name=site value='$settings[site]'>");
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
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


printc("Type:".helplink("pagetypes"));

if ($settings[add]) printc("<div class=desc>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br><br>");


if ($settings[add] || $settings[type] == 'page') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='page'".(($settings[type]=='page')?" checked":"").">");
	printc(" Content Page</b>".(($settings[type]=='page')?" - will allow you to add content to your site, along with user discussions if necessary.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'url') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='url'".(($settings[type]=='url')?" checked":"").">");
	printc(" Link/URL</b>".(($settings[type]=='url')?" - is a link to a page outside of your site (like <i>http://www.middlebury.edu/</i>). URLs will open in a new window, allowing users to continue browsing your site.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'heading') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='heading'".(($settings[type]=='heading')?" checked":"").">");
	printc(" Heading</b>".(($settings[type]=='heading')?" - appears in the navigation area, but with no link attached. It is useful to categorize your pages or links. Organization is sometimes a good thing.":"")."</div>");
}

if ($settings[add] || $settings[type] == 'divider') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($settings[add])?"onClick='typeChange()'":"")." value='divider'".(($settings[type]=='divider')?" checked":"").">");
	printc(" Divider</b>".(($settings[type]=='divider')?" - will put some sort of dividing space between items in your navigation list. This may just be some white space, or a thin line, depending on what theme you chose for your site.":"")."</div>");
}


if ($settings[type]=='divider') {
	printc("<div class=title>Divider</div>");
	printc("<div class=desc>Press <b>Add</b> below to add a divider to your left navigation list.</div>");
}
if ($settings[type]=='heading') {
	printc("<div class=title>Heading</div>");
	printc("<div class=desc>Enter a title for your header below and click the <b>Add</b> button.</div>");
}
if ($settings[type]=='url') {
	printc("<div class=title>URL</div>");
	printc("<div class=desc>Enter a title and URL for your link, and click the <b>Add</b> button.</div>");
}
if ($settings[type]=='page') {
	printc("<div class=title>Content Page</div>");
	printc("<div class=desc>Enter a title for your page below. When you are done click the <b>Add</b> button.</div>");
}



if ($settings[type] !='divider') {
	printc("<div class=leftmargin>");
	printc("Title: <input type=text size=35 name=title value='".spchars($settings[title])."'>");
	printc("<br>");
}

/*if ($settings[type]=='url' || $settings[type]=='page') {
//	printc("<div class=desc>Please enter the title for the page, url or heading, then select what type of addition this is. You may also choose divider, above.</div>");

	
	printc("<div class=title style='font-size: 14px'>Activation & Availability</div>");


	if ($mode) {
		printc("<table>");
		printc("<tr><td align=right>");
		printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($settings[activatedate])?" checked":"")."> <select name='activateday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($settings[activateday] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='activatemonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=$i" . (($settings[activatemonth] == $i)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='activateyear'>");
		$curryear = date("Y");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($settings[activateyear] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		
		printc("</td></tr>");
		
		printc("<tr><td align=right>");
		printc("Deactivate date:</td><td><input type=checkbox name='deactivatedate' value=1".(($settings[deactivatedate])?" checked":"")."> <select name='deactivateday'>");
		for ($i=1;$i<=31;$i++) {
			printc("<option" . (($settings[deactivateday] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>\n");
		printc("<select name='deactivatemonth'>");
		for ($i=0; $i<12; $i++) {
			printc("<option value=$i" . (($settings[deactivatemonth] == $i)?" selected":"") . ">$months[$i]\n");
		}
		printc("</select>\n<select name='deactivateyear'>");
		for ($i=$curryear; $i <= ($curryear+5); $i++) {
			printc("<option" . (($settings[deactivateyear] == $i)?" selected":"") . ">$i\n");
		}
		printc("</select>");
		
		printc("</tr></td></table>");
	}
	printc("Enabled: <input type=checkbox name='active' value=1" . (($settings[active])?" checked":"") . "><br><br>");
}*/

if ($settings[type] != 'divider') {
	printc("</div>");
}


if ($settings[type]=='url') {	
	printc("<div class=leftmargin>");
	printc("URL: <input type=text name=url value='$settings[url]' size=50>");
	printc("</div>");
}

if ($settings[type]=='page') {
	printc("<div class=leftmargin>");
	
	if ($mode) {
		// output permissions table
		$settings[editors] = db_get_value("sites","editors","name='$settings[site]'");
		if ($settings[editors] && $auser == $settings[site_owner]) {
			printc("<div class=title style='font-size: 14px'>Permissions (for editing page content)</div>");
			
			printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
			printc("<table width=100% style='border: 1px solid gray'>");
			printc("<tr><th width=50%>name</th><th colspan=3 width=40%>permissions</th></tr>");
			printc("<tr><th>&nbsp;</th><th>add</th><th>edit</th><th>delete</th></tr>");
			$edlist = explode(",",$settings[editors]);
			if (count($edlist)) {
				$color = 0;
				foreach ($edlist as $e) {
					printc("<tr><td class=td$color>");
					printc(ldapfname($e)." ($e)</td>");
					
					for ($i = 0; $i<3; $i++) {
						printc("<td class=td$color align=center>");
						printc("<input type=checkbox name='settings[permissions][$e][$i]' value=1".(($settings[permissions][$e][$i])?" checked":"").">");
						printc("</td>");
					}
					printc("</td>");
					printc("</tr>");
					$color = 1-$color;
				}
				
			}
			printc("</table>");
			if ($settings[editors]) printc("<div class=desc>Example: if you want <i>$e</i> to be able to add and delete content to/from this page, check the <b>Add</b> and <b>Delete</b> boxes associated with <i>$e</i>'s name. You also have the option later to lock content to prevent editor access.</div>");	

/* 			 */
/* 			$l = array("Add","Edit","Delete"); */
/* 		 */
/* 			$edlist = explode(",",$settings[editors]); */
/* 			foreach ($edlist as $e) { */
/* 			//	if (!ereg("([a-zA-Z])",$e)) next; */
/* 				printc($e . ": "); */
/* 				for ($i = 0; $i<3; $i++) { */
/* 					printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($permissions[$e][$i])?" checked":"")."> ".$l[$i]." "); */
/* 				} */
/* 				printc("<br>"); */
/* 			} */
/* 			 */
/* 			printc("<div class=desc>Example: if you want <i>$e</i> to be able to add and delete stories to/from this page, check the <b>Add</b> and <b>Delete</b> boxes associated with <i>$e</i>'s name.</div>"); */
/* 			printc("<br>"); */
		}
	
	
		printc("Show added/edited by <i>username</i> for content: <input type=checkbox name='showcreator' value=1". (($settings[showcreator])?" checked":"") . "><BR>");
		printc("<div class=desc>By checking the above box, the usernames of the people that added or edited the content on this page will be displayed along with the story text.</div>");
		
		printc("Show added/edited timestamp: <input type=checkbox name='showdate' value=1". (($settings[showdate])?" checked":"") . "><br>");
		printc("<div class=desc>By checking the above box, the date and time content was added (and edited, if that be the case), will be displayed along with the story text.</div>");
		
		printc("Allow editor discussion enabling: <input type=checkbox name='ediscussion'". (($settings[ediscussion])?" checked":"")."><br>");
		printc("<div class=desc>The above checkbox dictates if editors will be allowed to activate or deactivate discussion on specific content in this page.</div>");
		
		printc("Archive content on this site by: ");
		printc("<select name=archiveby>");
		$l = array("none"=>"Do not archive","week"=>"Week","month"=>"Month","year"=>"Year");
		foreach ($l as $n=>$v) {
			printc("<option value='$n'".(($settings[archiveby]==$n)?" selected":"").">$v\n");
		}
		printc("</select><br>");
		printc("<div class=desc>You can organize the content in this site by week added, month added, or year added. This useful for journal-like pages, where content is long and added often.</div>");
		
		if ($settings[site_owner] == $auser) {
			printc("Locked: <input type=checkbox name=locked value=1".(($settings[locked])?" checked":"").">");
			printc("<div class=desc>By checking the above box, you are preventing any editors from editing the content of this page of the site, regardless of their editor privileges.</div>");
			printc("<br>");
		} else printc("<input type=hidden name=locked value=$locked>");
	}
			
	printc("</div>");
}

if (!$mode) {
	$array = array("locked","archiveby","showcreator","showdate","ediscussion");
	foreach ($array as $ar) {
		printc("<input type=hidden name='$ar' value='".$$ar."'>");
	}
	// print out permissions
	if ($settings[permissions]) {
		foreach ($settings[permissions] as $e=>$p) {
			for ($i = 0; $i<3; $i++) {
				printc("<input type=hidden name='settings[permissions][$e][$i]' value='".$settings[permissions][$e][$i]."'>");
			}
		}
	}
}

printc("<br><br>");

printc("</form>");

