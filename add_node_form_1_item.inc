<? /* $Id$ */


printc("<form action='$PHP_SELF?$sid' method='post' name='addform'>");
printc("<input type='hidden' name='step' />");
printc("<input type='hidden' name='action' value='$action' />");
printc("<input type='hidden' name='typeswitch' value='0' />");

/******************************************************************************
 * Get permissions
 ******************************************************************************/

//if (!is_object($_SESSION[obj])) {
	$_SESSION[obj] =& new site($_REQUEST[site]);
	$_SESSION[obj]->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,1);
	$_SESSION[obj]->spiderDownLockedFlag();
	$_SESSION[obj]->buildPermissionsArray();
//}

$site_permissions = $_SESSION[obj]->getPermissions();

$sections = &$_SESSION[obj]->sections;

$section_pages = &$sections[$_REQUEST[section]]->pages;
$section_pages[$_REQUEST[page]]->buildPermissionsArray();


if ($_SESSION[settings][add]) printc("<div class='title'>Add Content for \"".$_REQUEST['link_title']."\" link in: ".$thisSection->getField("title")." > ".$thisPage->getField("title")."</div>");
else printc("<div class='title'>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
printc("<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("</td><td width='50%' align='right'>");
/* 	printc("<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n"); */
	//if ($_SESSION[new_node][type] =='page') printc("<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n");
	
if ($section_pages[$_REQUEST[page]]->hasPermissionDown("add")) {
	printc("<input type='submit' name='save' value='Continue  &rarr;' />\n");
}

printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
//printc("<input type='submit' name='save' value='Continue  &rarr;' />\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel' />");
printc("<input type='hidden' value='0' name='prevbutton' />");
printc("<input type='hidden' value='0' name='nextbutton' />");


printc("<div class='title'>Item Type:".helplink("wiki")."</div>");

printc("<table width='90%'cellspacing='5' border='0'>");
printc("<tr><td>");

/******************************************************************************
 * if user has permission to add to the current page 
 * then print out add node tupe and target
 ******************************************************************************/
	
if ($_SESSION[settings][add] && $section_pages[$_REQUEST[page]]->hasPermissionDown("add")) {

	/******************************************************************************
	 * Specify type of content to add (section, page or content)
	 ******************************************************************************/

	printc("Add a new: ");
	printc("<select name='type' onchange='typeChange()'>");
	
	// check to see if user is owner or has site add permission
	if ($_SESSION[obj]->owner == $_SESSION[auser] || $site_permissions[$_SESSION[auser]][0]) {
		printc("<option value='section'".(($_SESSION[settings][type] =='section')?" selected='selected'":"").">Section</option>");
	} else {
		printc("<option value='section' disabled='disabled'>Section</option>");
	}
	if ($sections[$_REQUEST[section]]->hasPermission("add")) {
		printc("<option value='page'".(($_SESSION[settings][type] =='page')?" selected='selected'":"").">Page</option>");	
	}else {
		printc("<option value='page' disabled='disabled'>Page</option>");
	}
	printc("<option value='content'".(($_SESSION[settings][type] =='content')?" selected='selected'":"").">Content</option>");
	printc("</select>");
	

	/******************************************************************************
	 * Specify where to add the content (site, section, page)
	 ******************************************************************************/
	
	if ($_SESSION[settings][type] =='section') {
		printc(" to this site");
	
	} else if ($_SESSION[settings][type] =='page') {
		printc(" to the: ");
		printc("<select name='location'>");
		
		foreach ($sections as $i=>$o) {
			$sections[$i]->buildPermissionsArray();
			if ($sections[$i]->hasPermissionDown("add") && $sections[$i]->hasPermission("add")) {
				printc("<option value='".$sections[$i]->id."'".(($_SESSION[settings][location] == $sections[$i]->id)?" selected='selected'":"").">".$sections[$i]->getField('title')."</option>");			
			} else {
				printc("<option value='".$sections[$i]->id." 'disabled='disabled'>".$sections[$i]->getField('title')."</option>");			
			}
			
		}		
		printc("</select> section");
		
	} else if ($_SESSION[settings][type] =='content') {
		$_SESSION[settings][location] = $_SESSION[settings][page];
		printc(" to: ");
		printc("<select name='location'>");
		
		foreach ($sections as $i=>$o) {
			$sections[$i]->buildPermissionsArray();
			if ($sections[$i]->hasPermissionDown("add")) {
				printc("<optgroup label ='".$sections[$i]->getField('title')."'>");
				$pages = &$sections[$i]->pages;
				foreach ($pages as $i=>$o) {
					if ($pages[$i]->hasPermissionDown("add") && $pages[$i]->getField('title') != "" && $pages[$i]->getField('type') != "rss") {
						printc("<option value='".$pages[$i]->id."'".(($_SESSION[settings][location] == $pages[$i]->id)?" selected='selected'":"").">".$pages[$i]->getField('title')."</option>");					
					} else if ($pages[$i]->getField('title') != "") {
						printc("<option value='".$pages[$i]->id." 'disabled='disabled'>".$pages[$i]->getField('title')."</option>");
					}
				}
				printc("<optgroup>");
			} 
		}
		printc("</select>");		
	}	
	printc("</td><td><br /><br />");
	
} else {
	printc("No target for this link has been created yet....");
}

printc("</td></tr>");
printc("<tr><td>");

if ($_SESSION[new_node][type] =='page') {
	printc("<div class='desc'>A new page is a sidebar link to a location where you can add any number of content blocks.</div>");
} else if ($_SESSION[new_node][type] =='content') {
	printc("<div class='desc'>New Content adds content to whatever page you specify in this site.</div>");
} else if ($_SESSION[new_node][type] =='section') {
	printc("<div class='desc'>A new section is a link to a location where you can add any number of pages</div>");
}
printc("</td><td></td></tr>");
printc("</table>");
printc("</form>");

