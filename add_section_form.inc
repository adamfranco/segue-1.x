<? // add_site_form.inc -- the form that people use to add/edit a site



printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

if ($edit) printc("<input type=hidden name=edit_section value=$edit_section>");
printc("<input type=hidden name=site value='$site'>");
printc("<input type=hidden name=section value=$section>");
printc("<input type=hidden name=page value=$page>");
printc("<input type=hidden name=action value='$action'>");
printc("<input type=hidden name=step value=2>");
printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

printc("<div class=title>Add Section to Site <b>".db_get_value("sites","title","name='$site'")."</b></div>");

// top bar w/ mode switch
printc("\n<table width=100%><tr>");
/* printc("<td align=left>Type: <select name=type onChange='typeChange()'>"); */
/* printc("<option value='section'".(($type=='section')?" selected":"").">Content Section"); */
/* printc("<option value='url'".(($type=='url')?" selected":"").">Link/URL"); */
/* printc("</select></td>"); */
printc("<td align=left>Type:".helplink("sectiontypes")."</td>");

printc("<td align=right><input type=submit name=switchmode value='Switch to ".(($mode)?"Basic":"Advanced")." view'></td>");
printc("\n</tr></table>");
printc("<div class=desc>Please choose above what type of content you would like to add to the top navigation bar. The choices are:</div>");
// end
printc("<div class=desc><b><input type=radio name=type onClick='typeChange()' value='section'".(($type=='section')?" checked":"").">Content Section</b>".(($type=='section')?" - allows you to create pages with content, such as class syllabi or a resume. (default)":"")."</div>");
printc("<div class=desc><b><input type=radio name=type onClick='typeChange()' value='url'".(($type=='url')?" checked":"").">Link/URL</b>".(($type=='url')?" - allows you to enter an internet address (such as <i>http://www.middlebury.edu/yourpage/</i>) to allows users to click on to look at a different website. Links will open in a new window, allowing people to continue browsing your site.":"")."</div>");


// big title output stuff junk
if ($type=='section') {
	printc("<div class=title>Content Section</div>");
}
if ($type=='url') {
	printc("<div class=title>URL to external page</div>");
}	

// -----------------------------
// Common Info

printc("<div class=leftmargin>");
printc("Title: <input type=text size=35 name=title value='".spchars($title)."'>");
printc("<br><br>");

printc("<div class=title style='font-size: 14px'>Activation & Availability</div>");
if ($mode) {
	printc("<table><tr><td align=right>");
	printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($activatedate)?" checked":"")."> <select name='activateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($activateday == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='activatemonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=$i" . (($activatemonth == $i)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='activateyear'>");
	$curryear = date("Y");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($activateyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	
	printc("</td></tr><tr><td align=right>");
	
	
	printc("Deactivate date:</td><td><input type=checkbox name='deactivatedate' value=1".(($deactivatedate)?" checked":"")."> <select name='deactivateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($deactivateday == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='deactivatemonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=$i" . (($deactivatemonth == $i)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='deactivateyear'>");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($deactivateyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	printc("</td></tr></table>");
}


printc("Enabled: <input type=checkbox name='active' value=1" . (($active)?" checked":"") . ">");

if ($edit && $mode) {
	printc("<div class=desc>");
	printc("If you would like to change the enabled setting to that above to all enclosed pages, please check this box: ");
	printc("<input type=checkbox name=recursiveenable value=1".(($recursiveenable)?" checked":"").">");
	if ($auser != $site_owner) printc("<BR><b>NOTE:</b> your ability to enable all enclosed pages on this site may be limited by the access privileges set for you by the site owner (<i>$site_owner</i>).");
	printc("</div>");
}


printc("</div>");
printc("<br><br>");

// -----------------------------
// URL
if ($type == 'url') {
	
	printc("<div class=leftmargin>");
	printc("URL: <input type=text name='url' value='$url' size=50>");
	printc("</div>");
	
	printc("<br><br>");
} else { printc("<input type=hidden name=url value='$url'>"); }

// -------------------------------
// SitesDB section
if ($mode) {
	if ($type=='section') {	
		printc("<div class=leftmargin>");
		// output permissions table
		$editors = db_get_value("sites","editors","name='$site'");
		if ($editors && $site_owner == $auser) {
		
			// begin permissions table
			
//			print "<pre>bla!";print_r($permissions);print "</pre>";
//			print "<pre>";print_r(decode_array(db_get_value("sites","permissions","name='$site'")));print "</pre>";
			printc("<div class=title style='font-size: 14px'>Permissions (for editing section pages)</div>");
			printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
			printc("<table width=100% style='border: 1px solid gray'>");
			printc("<tr><th width=50%>name</th><th colspan=3 width=40%>permissions</th></tr>");
			printc("<tr><th>&nbsp;</th><th>add</th><th>edit</th><th>delete</th></tr>");
			$edlist = explode(",",$editors);
			if (count($edlist)) {
				$color = 0;
				foreach ($edlist as $e) {
					printc("<tr><td class=td$color>");
					if ($edit) printc("<input type=checkbox name='copydownpermissions[]' value='$e' ".(($copydownpermissions && in_array($e,$copydownpermissions))?" checked":"")."> ");
					printc(ldapfname($e)." ($e)</td>");
					
					for ($i = 0; $i<3; $i++) {
						printc("<td class=td$color align=center>");
						printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($permissions[$e][$i])?" checked":"").">");
						printc("</td>");
					}
					printc("</td>");
					printc("</tr>");
					$color = 1-$color;
				}
				
			}
			printc("</table>");
			if ($editors) printc("<div class=desc>Example: if you want <i>$e</i> to be able to add and delete pages to/from this site, check the <b>Add</b> and <b>Delete</b> boxes associated with <i>$e</i>'s name. You also have the option later to lock parts of the site, preventing any editor access.</div>");	
			if ($edit) printc("<div class=desc><b>NOTE:</b> if you would like SitesDB to copy the permissions for a certain user down to all pages on this site, check the box to the left of their name.</div>");
		
		
		// end permissions table
		
		
/* 			printc("<div class=title style='font-size: 14px'>Permissions (for editing section pages)</div>"); */
/* 			 */
/* 			$l = array("Add","Edit","Delete"); */
/* 			 */
/* 			$edlist = explode(",",$editors); */
/* 			foreach ($edlist as $e) { */
/* 			//	if (!ereg("([a-zA-Z])",$e)) next; */
/* 				if ($edit) printc("<input type=checkbox name='copydownpermissions[]' value='$e' ".(($copydownpermissions && in_array($e,$copydownpermissions))?" checked":"")."> "); */
/* 				 */
/* 				printc($e . ": "); */
/* 				for ($i = 0; $i<3; $i++) { */
/* 					printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($permissions[$e][$i])?" checked":"")."> ".$l[$i]." "); */
/* 				} */
/* 				printc("<br>"); */
/* 			} */
/* 			printc("<div class=desc>Example: if you want <i>$e</i> to be able to add and delete pages to/from this site, check the <b>Add</b> and <b>Delete</b> boxes associated with <i>$e</i>'s name. You also have the option later to lock parts of the site, preventing any editor access.</div>"); */
/* 			if ($edit) printc("<div class=desc><b>NOTE:</b> if you would like SitesDB to copy the permissions for a certain user down to all pages on this site, check the box to the left of their name.</div>"); */
/* 			printc("<br>"); */
		}
		
		if ($site_owner == $auser) {
			printc("Locked: <input type=checkbox name=locked value=1".(($locked)?" checked":"").">");
			printc("<div class=desc>By checking the above box, you are preventing any editors from editing the content of this section of the site, regardless of their editor privileges.</div>");
			printc("<br>");
		} else printc("<input type=hidden name=locked value=$locked>");
		
		printc("<br>");
		
		printc("</div>");
	}
} else {
	$array = array("locked");
	foreach ($array as $ar) {
		printc("<input type=hidden name='$ar' value='".$$ar."'>");
	}
	if (!is_array($permissions)) $permissions = decode_array($permissions);
	// print out permissions
	if (count($permissions)) {
//		print_r($permissions);
		foreach ($permissions as $e=>$p) {
			for ($i = 0; $i<3; $i++) {
				printc("<input type=hidden name='permissions[$e][$i]' value='".$permissions[$e][$i]."'>");
			}
		}
	}
}

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid&action=viewsite&site=$site'>");
printc("<input type=submit value='&lt;&lt Back' name='goback'>\n");
printc("<input type=submit value='");
if ($add) printc("Add");
if ($edit) printc("Update");
printc("'>");

printc("</form>");

