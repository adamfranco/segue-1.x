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

//printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid&action=viewsite&site=$site&section=$section'>");
printc("<input type=submit value='&lt;&lt Back' name=goback>\n");
printc("<input type=submit value='");
if ($settings[add])printc("Add");
if ($settings[edit])printc("Update");
printc("'>");

printc("</form>");

