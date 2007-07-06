<?
// default page
$defaultStartQueries = $_totalQueries;

$pagetitle = "Segue";
$color = 0;
$sitesprinted=array();
//printpre($_SESSION);

/******************************************************************************
 * Display Options
 ******************************************************************************/

if (isset($_REQUEST[expand_pastclasses]) && $_REQUEST[expand_pastclasses] == 'true')
	$_SESSION["expand_pastclasses"] = true;
else if (isset($_REQUEST[expand_pastclasses]) && $_REQUEST[expand_pastclasses] == 'false')
	$_SESSION["expand_pastclasses"] = false;
else if (!isset($_SESSION["expand_pastclasses"]))
	$_SESSION["expand_pastclasses"] = false;

if (isset($_REQUEST[expand_editorsites]) && $_REQUEST[expand_editorsites] == 'true')
	$_SESSION["expand_editorsites"] = true;
else if (isset($_REQUEST[expand_editorsites]) && $_REQUEST[expand_editorsites] == 'false')
	$_SESSION["expand_editorsites"] = false;
else if (!isset($_SESSION["expand_editorsites"]))
	$_SESSION["expand_editorsites"] = false;

if (isset($_REQUEST[expand_personalsites]) && $_REQUEST[expand_personalsites] == 'true')
	$_SESSION["expand_personalsites"] = true;
else if (isset($_REQUEST[expand_personalsites]) && $_REQUEST[expand_personalsites] == 'false')
	$_SESSION["expand_personalsites"] = false;
else if (!isset($_SESSION["expand_personalsites"]))
	$_SESSION["expand_personalsites"] = false;
	
if (isset($_REQUEST[expand_othersites]) && $_REQUEST[expand_othersites] == 'true')
	$_SESSION["expand_othersites"] = true;
else if (isset($_REQUEST[expand_othersites]) && $_REQUEST[expand_othersites] == 'false')
	$_SESSION["expand_othersites"] = false;
else if (!isset($_SESSION["expand_othersites"]))
	$_SESSION["expand_othersites"] = false;
	
if (isset($_REQUEST[expand_upcomingclasses]) && $_REQUEST[expand_upcomingclasses] == 'true')
	$_SESSION["expand_upcomingclasses"] = true;
else if (isset($_REQUEST[expand_upcomingclasses]) && $_REQUEST[expand_upcomingclasses] == 'false')
	$_SESSION["expand_upcomingclasses"] = false;
else if (!isset($_SESSION["expand_upcomingclasses"]))
	$_SESSION["expand_upcomingclasses"] = false;
	
if (isset($_REQUEST[expand_recentactivity]) && $_REQUEST[expand_recentactivity] == 'true')
	$_SESSION["expand_recentactivity"] = true;
else if (isset($_REQUEST[expand_recentactivity]) && $_REQUEST[expand_recentactivity] == 'false')
	$_SESSION["expand_recentactivity"] = false;
else if (!isset($_SESSION["expand_recentactivity"]))
	$_SESSION["expand_recentactivity"] = false;


/******************************************************************************
 * public site listing link
 ******************************************************************************/
$leftnav_extra .= <<< END

<table width="100%" border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td height="100%" valign="bottom" style="font-weight: bolder">
		<a href='sitelisting.php?$sid' onclick='doWindow("listing",600,500)' target='listing'>Site Listing</a>
		</td>
	</tr>
</table>
END;

/******************************************************************************
 * handle site copy
 ******************************************************************************/
if ($copysite && $newname && $origname) {
	$origSite =& new site($origname);
	$origSite->fetchDown(1);

	/******************************************************************************
	 * Check to make sure that the slot is not already in use.
	 * Hitting refresh after copying a site, will insert a second copy of the site
	 * if we don't check for this.
	 ******************************************************************************/
	$query = "SELECT FK_site FROM slot WHERE slot_name = '".addslashes($newname)."'";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	if (!$a[FK_site]) {
		$copyDiscussions = ($_REQUEST['copy_discussions'])?TRUE:FALSE;
		$origSite->copySite($newname, TRUE, $copyDiscussions);
		log_entry("copy_site","$_SESSION[auser] copied site ".$origname." to ".$newname,$newname,$origSite->id,"site"); // Should maybe be the newsite's id.
	}
}

/******************************************************************************
 * Links to other segue instances
 ******************************************************************************/
if ($allowclasssites != $allowpersonalsites && 
	($personalsitesurl != "" || $classsitesurl != "")) {
	if ($allowclasssites) {
		add_link(topnav,"Classes");
		add_link(topnav,"Community","$personalsitesurl",'','','');
	} else {
		add_link(topnav,"Classes","$classsitesurl",'','','');
		add_link(topnav,"Community");
	}
}


/******************************************************************************
 * Output page, get classes, etc.
 ******************************************************************************/
if ($_loggedin) {

	//add_link(leftnav,"Links");
	foreach ($defaultlinks as $t=>$u)
		add_link(leftnav,$t,"http://".$u,'','',"_blank");
	
	add_link(leftnav,helplink("index"),"1");

	/******************************************************************************
	 * List sites
	 ******************************************************************************/

	printc("\n<div align='right'>\n\t<a href='email.php?$sid&amp;action=user&amp;from=home' onclick='doWindow(\"email\",700,500)' target='email'>Your Posts</a>\n</div>");	

	/*********************************************************
	 * Fetch all of the info for all of the sites and slots
	 * that the user is an editor or owner for, so we don't have
	 * to get them again.
	 *********************************************************/
	// this should include all sites that the user owns as well.
	
	
		$userOwnedSlots = slot::getSlotInfoWhereUserOwner($_SESSION['auser']);
		if (!is_array($userOwnedSlots) || !array_key_exists($_SESSION['auser'], $userOwnedSlots)) {
				$userOwnedSlots[$_SESSION['auser']] = array();
				$userOwnedSlots[$_SESSION['auser']]['slot_name'] = $_SESSION['auser'];
				$userOwnedSlots[$_SESSION['auser']]['slot_type'] = 'personal';
				$userOwnedSlots[$_SESSION['auser']]['slot_owner'] = $_SESSION['auser'];
				$userOwnedSlots[$_SESSION['auser']]['site_exits'] = false;		
		}
		
	if ($_SESSION["expand_othersites"] != 0 || $_SESSION["expand_editorsites"] != 0 || $_SESSION["expand_pastclasses"] != 0 || $_SESSION["expand_personalsites"] != 0) {
		// Add any user-owned groups that aren't already in the slot list
		$userOwnedGroups = group::getGroupsOwnedBy($_SESSION['auser']);
		foreach ($userOwnedGroups as $classSiteName) {
			if (!isset($userOwnedSlots[$classSiteName])) {
				$userOwnedSlots[$classSiteName] = array();
				$userOwnedSlots[$classSiteName]['slot_name'] = $classSiteName;
				$userOwnedSlots[$classSiteName]['slot_type'] = 'class';
				$userOwnedSlots[$classSiteName]['slot_owner'] = $_SESSION['auser'];
				$userOwnedSlots[$classSiteName]['site_exits'] = false;
			}
		}
		if ($_SESSION["expand_editorsites"] != 0) {
			$siteLevelEditorSites = segue::getSiteInfoWhereUserIsSiteLevelEditor($_SESSION['auser']);		
			$anyLevelEditorSites = segue::getSiteInfoWhereUserIsEditor($_SESSION['auser']);
		}
	}
	
	
	$usersCurrentClasses = $classes;
	$usersOldClasses = $oldclasses;
	$usersFutureClasses = $futureclasses;
	$usersAllClasses = $allclasses[$_SESSION['auser']];
	
	// replace groupclasses with their groups
	$classgroupLists = getClassgroupListsForGroupsContainingClasses(array_keys($usersAllClasses));
	foreach ($classgroupLists as $groupName => $classgroupList) {
		foreach ($classgroupList as $className => $classParts) {
			// Make a virtual group-code to sort with.
			// Note: this assumes (for ordering purposes), that all classes in the
			// group are in the same semester/year.
			$groupParts = array(
						'code' => $groupName,
						'sect' => '',
						'sem' => $classParts['sem'],
						'year' => $classParts['year']
					);
			
			if (isset($usersCurrentClasses[$className])) {
				unset($usersCurrentClasses[$className]);
				if (!isset($usersCurrentClasses[$groupName]))
					$usersCurrentClasses[$groupName] = $groupParts;
			}
			
			if (isset($usersOldClasses[$className])) {
				unset($usersOldClasses[$className]);
				if (!isset($usersOldClasses[$groupName]))
					$usersOldClasses[$groupName] = $groupParts;
			}
			
			if (isset($usersFutureClasses[$className])) {
				unset($usersFutureClasses[$className]);
				if (!isset($usersFutureClasses[$groupName]))
					$usersFutureClasses[$groupName] = $groupParts;
			}
			
			if (isset($usersAllClasses[$className])) {
				unset($usersAllClasses[$className]);
				if (!isset($usersAllClasses[$groupName]))
					$usersAllClasses[$groupName] = $groupParts;
			}
		}
	}
	
	 // Sort the classes
	$usersCurrentClasses = array_keys(sortClasses($usersCurrentClasses));
	$usersOldClasses = array_keys(sortClasses($usersOldClasses));
	$usersFutureClasses = array_keys(sortClasses($usersFutureClasses));
	$usersAllClasses = array_keys(sortClasses($usersAllClasses));
	
	// Fetch all of the class info
	$usersAllClassesInfo = slot::getSlotInfoForSlots($usersAllClasses);
	foreach ($usersAllClasses as $classSiteName) {
		if (!isset($usersAllClassesInfo[$classSiteName])) {
			$usersAllClassesInfo[$classSiteName] = array();
			$usersAllClassesInfo[$classSiteName]['slot_name'] = $classSiteName;
			$usersAllClassesInfo[$classSiteName]['slot_type'] = 'class';
			$usersAllClassesInfo[$classSiteName]['slot_owner'] =	null;
			$usersAllClassesInfo[$classSiteName]['site_exits'] = false;
		}
	}
	
// visitor are users who post to public discussions w/o logging in
// visitors are not allowed to create sites
if ($_SESSION[atype] == 'visitor') {
	printc("Welcome to Segue.  You have a visitor account that was created when you registered with Segue.  ");
	printc("This account will allow you to post to any public discussions ");
	printc("and view all publically accessible sites.<br /><br />");
} else if ($_SESSION[atype] == 'guest') {
	printc("Welcome to Segue.  You have been given a guest account.  ");
	printc("This account will allow you to view sites and post to discussions/assessments");
	printc("that are limited to users in the ".$cfg[inst_name]." community.<br /><br />");
}

printc("\n<table width='100%'>");

/******************************************************************************
 * Recent Activity
 ******************************************************************************/
if ($_SESSION["expand_recentactivity"] == 0) {
	printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_recentactivity=true'>+</a> Recent Activity</td>\n\t\t</tr>");
} else {
	printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_recentactivity=false'>-</a> Recent Activity</td>\n\t\t</tr>");
}
printc("\n</table>");

if ($_SESSION["expand_recentactivity"] != 0) {	

	printc("<table border='0' width='100%' align ='center' cellpadding='0' cellspacing='5'>");
	printc("<tr><td valign='top'>");

	//recent discussions
	
	//pagination variables
	if (isset($_REQUEST["discussion_set"]) && $_REQUEST["discussion_set"] > 0) 
		$_SESSION["discussion_set"] = intval($_REQUEST["discussion_set"]);
	else if (!isset($_SESSION["discussion_set"]))
		$_SESSION["discussion_set"] = 1;
	
	$num_per_set = 10;
		
	$start = ($_SESSION["discussion_set"] - 1) * $num_per_set;
	$end = $start + $num_per_set;
	
	$recent_discussions = recent_discussions($start, $num_per_set, $_SESSION["aid"]);
	$number_in_batch = db_num_rows($recent_discussions);

	if ($number_in_batch > 0 || $start > 0) {	
		$recent_discussions_sites = "\n<table border='0' width='100%' align='center' cellpadding='1' cellspacing='0'>";
		$recent_discussions_sites .= "\n\t<tr><td colspan='4' align='left' class='title2'>Recent Discussions";
		//print out headers
		$recent_discussions_sites .= "</td></tr>";
		$recent_discussions_sites .= "\n\t<tr>\n\t\t<td class='title3'>Date/Time</td>\n\t\t<td class='title3'>Participant</td>\n\t\t<td class='title3'>Subject</td>\n\t\t<td class='title3'>Site</td>\n\t</tr>";
		
// 		printpre($start);
// 		printpre($end);
// 		printpre($number_in_batch);
		
		while ($a = db_fetch_assoc($recent_discussions)) {			
			$recent_discussions_sites .= "<tr>";
			preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2}) ([0-9]{2}:[0-9]{2}):[0-9]{2}/', $a['discussion_tstamp'], $matches);
			//$tstamp =& TimeStamp::fromString($tstamp);
			//$time =& $tstamp->asTime();
			//$recent_discussions_sites .= "<td valign='top' class='list'>".$tstamp->ymdString()."<br/>".$time->string12(true)."</td>";
			$recent_discussions_sites .= "\n\t<td valign='top' class='list' style='white-space: nowrap;'>".$matches[1]." &nbsp; ".$matches[2]."</td>";
			$recent_discussions_sites .= "\n\t<td valign='top' class='list'><a href='$PHP_SELF?type=recent&amp;user=".$a['user_uname']."'>".$a['user_fname']."</a></td>";
			
			$recent_discussions_sites .= "\n\t<td valign='top' class='list'><a href='".$_full_uri."/index.php?&amp;site=".$a['slot_name'];
			$recent_discussions_sites .= "&amp;action=site&amp;section=".$a['section_id']."&amp;page=".$a['page_id']."&amp;story=".$a['story_id'];
			$recent_discussions_sites .= "&amp;detail=".$a['story_id']."#".$a['discussion_id'];
			$recent_discussions_sites .= "' target='new_window'>";
			$recent_discussions_sites .= urldecode($a['discussion_subject'])."</a></td>";
			
			$recent_discussions_sites .= "\n\t<td valign='top' class='list'><a href='".$_full_uri."/index.php?&amp;site=".$a['slot_name'];
			$recent_discussions_sites .= "&amp;action=site&amp;section=".$a['section_id']."&amp;page=".$a['page_id']."&amp;story=".$a['story_id'];
			$recent_discussions_sites .= "&amp;detail=".$a['story_id'];
			$recent_discussions_sites .= "' target='new_window'>".$a['site_title']."</a></td>";
			$recent_discussions_sites .= "\n\t</tr>";
		}
		
		// print out discussion pagination
		$pagelinks = array();		
		if ($num_per_set != 0)  {
			$recent_discussions_sites .= "\n\t<tr><td colspan='4' align='left'>";
			$recent_discussions_sites .= "\n\t<div class='multi_page_links'>";
			
			for ($i = 1; $i <= ($start / $num_per_set); $i++) {
				$pagelinks[] = "?$sid&amp;discussion_set=".$i;
				$recent_discussions_sites .= "\n\t\t<a href='?$sid&amp;discussion_set=".$i."'>".$i."</a> | ";
			}
			
			// The current one is the last one printed
			$pagelinks[] = "current";
			$recent_discussions_sites .= "\n\t\t<strong>".$i."</strong> ";
			
			// Next links if there are more results
			if ($number_in_batch >= $num_per_set) {
				$i++;
				$recent_discussions_sites .= " | \n\t\t<a href='?$sid&amp;discussion_set=".$i."'>".$i." >></a> ";
			}
			
			$recent_discussions_sites .= "\n\t</div>\n\t</td>\n\t</tr>";
		}
		
		
		
		
		
		$recent_discussions_sites .= "\n</table>";
		printc($recent_discussions_sites);
	}	

	printc("\n\t</td>\n\t</tr>\n\t<tr>\n\t<td valign='top'>");
	
	// recently edited content
	$recentComponents = recent_edited_components(10, $_SESSION["aid"]);
	
	
	if (count($recentComponents)) {	
		$number_recent_sites = count($recentComponents);
		$edited_sites = "\n\t\t<table border='0' width='100%' align ='center' cellpadding='1' cellspacing='0'>";
		$edited_sites .= "\n\t\t\t<tr>\n\t\t\t<td colspan='3' align='left' class='title2'>Your Recent Edits";
		$edited_sites .="</td>\n\t\t\t</tr>";
		$edited_sites .= "\n\t\t\t<tr>\n\t\t\t<td class='title3'>Date</td>\n\t\t\t<td class='title3'>Site</td>\n\t\t\t<td class='title3'>Most Recent Edit...</td>\n\t\t\t</tr>";

		foreach ($recentComponents as $a) {
			$url = $_full_uri."/index.php?";
			$url .= "&amp;action=site&amp;site=".$a['slot_name'];
			if ($a['mr_section_id'])
				$url .= "&amp;section=".$a['mr_section_id'];
			if ($a['mr_page_id'])
				$url .= "&amp;page=".$a['mr_page_id'];
			if ($a['mr_story_id'])
				$url .= "&amp;story=".$a['mr_story_id'];
			
//			printpre($a);
			$edited_sites .= "\n\t\t\t<tr>";
			preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})/', $a['most_recent_tstamp'], $matches);
			$edited_sites .= "\n\t\t\t<td valign='top' class='list'>".$matches[1]."</td>";
			$edited_sites .= "\n\t\t\t<td valign='top' class='list' style='text-align: left; width: 25%; padding-left: 5px;'> <a href=\"".$url."\" target='new_window'>".$a['site_title']."</a></td>";
			$edited_sites .= "\n\t\t\t<td valign='top' class='list'><a href=\"".$url."\" target='new_window'>";				
	//		$edited_sites .= $a['site_title'];
			if ($a['mr_section_title'] != "") {
				$edited_sites .= " > ".$a['mr_section_title'];
			}
			if ($a['mr_page_title'] != "") {
				$edited_sites .= " > ".$a['mr_page_title'];
			}
			if ($a['mr_story_title'] != "") {
				$edited_sites .= " > ".$a['mr_story_title'];
			}
			$edited_sites .= "</a></td>";
			$edited_sites .= "\n\t\t\t</tr>";
		}
		$edited_sites .= "\n\t\t</table>";
		printc($edited_sites);
	}	
	
	printc("\n\t</td>\n\t</tr>\n</table>");
}
	
	/******************************************************************************
	 * Print out classes
	 ******************************************************************************/
	
	if ($allowclasssites) {
		$_class_list_titles = array("usersCurrentClasses"=>"Your Current Classes",
									"usersFutureClasses"=>"Upcoming Classes",
									"usersOldClasses"=>"Previous Semesters");
		
		/*********************************************************
		 * Class Sites for students
		 *********************************************************/
		if ($_SESSION[atype]=='stud') {
			
			//loop through all classes in list
			foreach ($_class_list_titles as $timePeriod => $title) {
				
				/******************************************************************************
				 * Current Classes Title
				 ******************************************************************************/
				
				if ($timePeriod == "usersCurrentClasses") {
					printc("\n<table border='0' width='100%'>");
					printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'>Current Class Sites</td>\n\t\t</tr>");
					printc("\n\t\t\t\t<tr>\n\t\t\t\t\t<th>class</th>\n\t\t\t\t\t<th>site</th>\n\t\t\t\t</tr>");
					$groupsPrinted = array();
					foreach ($usersCurrentClasses as $className) {
						if ($classSiteName = group::getNameFromClass($className)) {
							if ($groupsPrinted[$classSiteName])
								continue;
							
							$groupsPrinted[$classSiteName] = true;
						} else {
							$classSiteName = $className;
						}
						
					
						if (isset($userOwnedSlots[$classSiteName]))
							printStudentSiteLine($classSiteName, $userOwnedSlots[$classSiteName]);
							
						else if (isset($anyLevelEditorSites[$classSiteName]))
							printStudentSiteLine($classSiteName, $anyLevelEditorSites[$classSiteName]);
				
						else if (isset($usersAllClassesInfo[$classSiteName]))
							printStudentSiteLine($classSiteName, $usersAllClassesInfo[$classSiteName]);
							
						else
							printc("\n\t\t\t\t<tr>\n\t\t\t\t<td colspan='2' style='background-color: red; font-weight: bold'>There was an error loading information for site: ".$classSiteName."\n\t\t\t\t\t</td>\n\t\t\t\t</tr>");
					}
				
					printc("\n\t\t\t</table>");
					
				} else if ($timePeriod == "usersOldClasses") {
						
					/******************************************************************************
					 * expand/collapse link for previous sites listing
					 ******************************************************************************/		
					if ($timePeriod == "usersOldClasses") {
						printc("<table border='0' width='100%'>");
						if (!$_SESSION["expand_pastclasses"]) {
							printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_pastclasses=true'>+</a> $title \n\t\t</td></tr>");

						} else {
							printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_pastclasses=false'>-</a> $title \n\t\t</td></tr>");
							printc("\n\t<tr>");
							printc("\n\t\t<td valign='top'>");
						}
					}
							
					if ($_SESSION["expand_pastclasses"] == 0 && $timePeriod == "usersOldClasses") {
						// do nothing
					} else {																			
						printc("\n\t\t\t<table width='100%'>\n\t\t\t\t<tr>\n\t\t\t\t\t<th>class</th>\n\t\t\t\t\t<th>site</th>\n\t\t\t\t</tr>");
						
						$groupsPrinted = array();
						foreach ($$timePeriod as $className) {
							if ($classSiteName = group::getNameFromClass($className)) {
								if ($groupsPrinted[$classSiteName])
									continue;
								
								$groupsPrinted[$classSiteName] = true;
							} else {
								$classSiteName = $className;
							}
							
							// Your other sites
							if (isset($userOwnedSlots[$classSiteName]))
								printStudentSiteLine($classSiteName, $userOwnedSlots[$classSiteName]);
							
							//Sites you can edit
							else if (isset($anyLevelEditorSites[$classSiteName]))
								printStudentSiteLine($classSiteName, $anyLevelEditorSites[$classSiteName]);
					
							else if (isset($usersAllClassesInfo[$classSiteName]))
								printStudentSiteLine($classSiteName, $usersAllClassesInfo[$classSiteName]);
								
							else
								printc("\n\t\t\t\t<tr>\n\t\t\t\t<td colspan='2' style='background-color: red; font-weight: bold'>There was an error loading information for site: ".$classSiteName."\n\t\t\t\t\t</td>\n\t\t\t\t</tr>");
						}
						
						printc("\n\t\t\t</table>");
						printc("\n\t\t</td>");
						printc("\n\t</tr>");						
					}
					

				}
			}
			printc("\n</table>");
	
		}
	}

	 /******************************************************************************
	 * handle group adding backend here
	 ******************************************************************************/
	if (count($_REQUEST[group]) && ($_REQUEST[newgroup] || $_REQUEST[groupname])) { // they chose a group
		if (!$_REQUEST[newgroup]) $_REQUEST[newgroup] = $_REQUEST[groupname];
		if (ereg("^[a-zA-Z0-9_-]{1,20}$",$_REQUEST[newgroup])) {
			$groupObj = new group($_REQUEST[newgroup],$_SESSION[auser]);
			if (group::exists($_REQUEST[newgroup])) { // already exists
				if ($groupObj->fetchFromDB()) {
					$groupObj->addClasses($_REQUEST[group]);
					$groupObj->updateDB();
					$list = implode(",",$groupObj->classes);
/*					   log_entry("classgroups","$_SESSION[auser] updated $_REQUEST[newgroup] to be $list","$_REQUEST[newgroup]"); */
					log_entry("classgroups","$_SESSION[auser] updated $_REQUEST[newgroup] to be $list","NULL",$groupObj->id,"classgroup");
				} else error("Somebody has already created a class group with that name. Please try another name.");
			} else {	// new group
				$groupObj->addClasses($_REQUEST[group]);
				$groupObj->updateDB();
				log_entry("classgroups","$_SESSION[auser] added $_REQUEST[newgroup] with ".implode(",",$groupObj->classes),"NULL",$groupObj->id,"classgroup");
			}
		} else
			error("Your group name is invalid. It may only contain alphanumeric characters, '_', '-', and be under 21 characters. No spaces, punctuation, etc.");

	}
	

//	printc("\n<div class='title'>Sites".helplink("sites")."</div>");
	
	printc("\n<form name='groupform' action='$PHP_SELF?$sid&amp;action=default' method='post'>");
	
	printc("\n\t<table width='100%'>");
	

/*********************************************************
 * Personal Sites
 *********************************************************/
// 	if ($allowpersonalsites) {
// 		// print out the personal site if there is a slot for them that they own.
// 		if ($userOwnedSlots[$_SESSION['auser']]['slot_owner'] == $_SESSION['auser']) {
// 			// visitor are users who post to public discussions w/o logging in
// 			// visitors are not allowed to create sites
// 			if ($_SESSION[atype] == 'visitor') {
// 				printc("Welcome to Segue.  You have a visitor account that was created when you registered with Segue.  ");
// 				printc("This account will allow you to post to any public discussions ");
// 				printc("and view all publically accessible sites.<br /><br />");
// 			} else if ($_SESSION[atype] == 'guest') {
// 				printc("Welcome to Segue.  You have been given a guest account.  ");
// 				printc("This account will allow you to view sites and post to discussions/assessments");
// 				printc("that are limited to users in the ".$cfg[inst_name]." community.<br /><br />");
// 			} else {
// 				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'>Personal Site</td>\n\t\t</tr>");
// 				printSiteLine2($userOwnedSlots[$_SESSION['auser']]);
// 			}
// 		}
// 	}
	
/*********************************************************
 * Personal Sites
 *********************************************************/
	 if ($allowpersonalsites) {
	 	// visitor are users who post to public discussions w/o logging in
		// visitors are not allowed to create sites
		if ($_SESSION[atype] != 'visitor' && $_SESSION[atype] != 'guest') {
			if ($_SESSION["expand_personalsites"] == 0) {
				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_personalsites=true'>+</a> Personal Sites</td>\n\t\t</tr>");
			} else {
				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_personalsites=false'>-</a> Personal Sites</td>\n\t\t</tr>");
			}
			
			if ($_SESSION["expand_personalsites"] != 0) {
			
				$sites=array();
				if (is_array($userOwnedSlots)) {
					foreach (array_keys($userOwnedSlots) as $name) {
						$info =& $userOwnedSlots[$name];
						
						if (!in_array($name, $sitesprinted) && $info['slot_type'] == 'personal') {
							$sites[$name] =& $info;
						}
					}
				}
				
				if (count($sites)) {
					foreach (array_keys($sites) as $name)
						printSiteLine2($sites[$name]);
				}
				unset($sites);
			}
		}
	}

/*********************************************************
 * Class sites for professors
 *********************************************************/
	if ($allowclasssites) {
		//class sites for professors (for student see above)
		if ($_SESSION['atype'] == 'prof' || $_SESSION['atype'] == 'admin') {
			
			//current classes
			if (count($classes)) {
				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'>Current Class Sites</td>\n\t\t</tr>");
				$groupsPrinted = array();
				foreach ($usersCurrentClasses as $className) {
					if ($classSiteName = group::getNameFromClass($className)) {
						if ($groupsPrinted[$classSiteName])
							continue;
						
						$groupsPrinted[$classSiteName] = true;
					} else {
						$classSiteName = $className;
					}
					
					if (isset($userOwnedSlots[$classSiteName]))
						printSiteLine2($userOwnedSlots[$classSiteName], 0, 1, $_SESSION[atype]);
						
					else if (isset($anyLevelEditorSites[$classSiteName]))
						printSiteLine2($anyLevelEditorSites[$classSiteName], 0, 1, $_SESSION[atype]);
			
					else if (isset($usersAllClassesInfo[$classSiteName]))
						printSiteLine2($usersAllClassesInfo[$classSiteName], 0, 1, $_SESSION[atype]);
						
					else
						printc("\n\t\t<tr>\n\t\t\t<td colspan='2' style='background-color: red; font-weight: bold'>There was an error loading information for site: ".$classSiteName."</td>\n\t\t</tr>");
				}
			}

			//upcoming classes
				if (count($usersFutureClasses)) {
					
					if ($_SESSION["expand_upcomingclasses"] == 0) {
						printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_upcomingclasses=true'>+</a> Upcoming Classes</td>\n\t\t</tr>");
					} else {
						printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_upcomingclasses=false'>-</a> Upcoming Classes</td>\n\t\t</tr>");
									
						foreach ($usersFutureClasses as $className) {
							if ($classSiteName = group::getNameFromClass($className)) {
								if ($groupsPrinted[$classSiteName]) {
									continue;
								}
								$groupsPrinted[$classSiteName] = true;
							} else {
								$classSiteName = $className;
							}
							
							if (isset($userOwnedSlots[$classSiteName]))
								printSiteLine2($userOwnedSlots[$classSiteName], 0, 1, $_SESSION[atype]);
								
							else if (isset($anyLevelEditorSites[$classSiteName]))
								printSiteLine2($anyLevelEditorSites[$classSiteName], 0, 1, $_SESSION[atype]);
					
							else if (isset($usersAllClassesInfo[$classSiteName]))
								printSiteLine2($usersAllClassesInfo[$classSiteName], 0, 1, $_SESSION[atype]);
								
							else
								printc("\n\t\t<tr>\n\t\t\t<td colspan='2'>There was an error loading information for site: ".$classSiteName."</td>\n\t\t</tr>");
						}
					}
				}
			

			
			//info/interface for groups
			if (count($classes) || count($usersFutureClasses)) {
				printc("\n\t\t<tr>\n\t\t\t<th colspan='2' align='right'>add checked sites to group: \n\t\t\t\t<input type='text' name='newgroup' size='10' class='textfield' />");
				$havegroups = count($userOwnedGroups);
				if ($havegroups) {
					printc(" \n\t\t\t\t<select name='groupname' onchange='document.groupform.newgroup.value = document.groupform.groupname.value'>");
					printc("\n\t\t\t\t\t<option value=''>-choose-</option>");
					foreach ($userOwnedGroups as $group) {
						printc("\n\t\t\t\t\t<option value='$group'>$group</option>");
					}
					printc("\n\t\t\t\t</select>");
				}
				printc(" \n\t\t\t\t<input type='submit' class='button' value='add' />");
				printc("\n\t\t\t</th>\n\t\t</tr>");
				printc("\n\t\t<tr>\n\t\t\t<th colspan='2' align='left'>");
				printc("\n\t\t\t\t<div style='padding-left: 10px; font-size: 10px;'>By adding sites to a group you can consolidate multiple class sites into one entity. This is useful if you teach multiple sections of the same class and want to work on only one site for those classes/sections. Check the boxes next to the classes you would like to add, and either type in a new group name or choose an existing one.</div>");
				if ($havegroups) printc("\n\t\t\t\t<div class='desc'>\n\t\t\t\t\t<a href='edit_groups.php?$sid' target='groupeditor' onclick='doWindow(\"groupeditor\",400,400)'>[edit class groups]</a>\n\t\t\t\t</div>");
				printc("\n\t\t\t</th>\n\t\t</tr>");
			}
				
			//past classes
			if ($_SESSION["expand_pastclasses"] == 0) {
				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_pastclasses=true'>+</a> Past Classes</td>\n\t\t</tr>");
			} else {
				printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_pastclasses=false'>-</a> Past Classes</td>\n\t\t</tr>");
			}
			
			if ($_SESSION["expand_pastclasses"] != 0) {	
					
				$sites=array();
				if (is_array($userOwnedSlots)) {

					foreach (array_keys($userOwnedSlots) as $name) {
						$info =& $userOwnedSlots[$name];
						
						if (!in_array($name, $sitesprinted)) {
							if ($allowclasssites && !$allowpersonalsites) {
								if ($info['slot_type'] != 'personal' && $info['slot_type'] == 'class')
									$sites[$name] =& $info;
							
							} else if (!$allowclasssites && $allowpersonalsites) {
								if ($info['slot_type'] == 'personal')
									$sites[$name] =& $info;
				
							} else
								$sites[$name] =& $info;
						}
					}
				}

				if (count($sites)) {
					foreach (array_keys($sites) as $name)
						printSiteLine2($sites[$name]);
				}
				unset($sites);
	
			}
		}
			
	}

	/*********************************************************
	 * Other sites where user is owner (student, staff or faculty
	 *********************************************************/
	if ($allowclasssites) {
		if ($_SESSION["expand_othersites"] == 0) {
			printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_othersites=true'>+</a> Your Other Sites".helplink("othersites","?")."</td>\n\t\t</tr>");
		} else {
			printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_othersites=false'>-</a> Your Other Sites".helplink("othersites","?")."</td>\n\t\t</tr>");
		}
		
		if ($_SESSION["expand_othersites"] != 0) {
		
			$sites=array();
			if (is_array($userOwnedSlots)) {
				foreach (array_keys($userOwnedSlots) as $name) {
					$info =& $userOwnedSlots[$name];
					
					if (!in_array($name, $sitesprinted)) {
						if ($info['slot_type'] != 'personal' && $info['slot_type'] != 'class') {
							$sites[$name] =& $info;
						}
					}
				}
			}
			
			if (count($sites)) {
				foreach (array_keys($sites) as $name)
					printSiteLine2($sites[$name]);
			}
			unset($sites);
		}
	}
	
	/******************************************************************************
	 * sites where the user is an Editor
	 ******************************************************************************/
	if ($_SESSION["expand_editorsites"] == 0) {
		printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_editorsites=true'>+</a> Sites you can edit".helplink("othersites","?")."</td>\n\t\t</tr>");
	} else {
		printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'><a href='$PHP_SELF?expand_editorsites=false'>-</a> Sites you can edit".helplink("othersites","?")."</td>\n\t\t</tr>");
	}
	
	if ($_SESSION["expand_editorsites"]) {

		$sites = array();
		if (is_array($anyLevelEditorSites)) {
			foreach (array_keys($anyLevelEditorSites) as $name) {
				$info =& $anyLevelEditorSites[$name];
				
				if (!in_array($name, $sitesprinted) 
					&& ($info['hasPermissionDownA']
						|| $info['hasPermissionDownE']
						|| $info['hasPermissionDownD'])
					&& $_SESSION['auser'] !=  $info['slot_owner']) 
				{
					if ($allowclasssites && !$allowpersonalsites) {
						if($info['slot_type'] != 'personal')
							$sites[$name] =& $info;
					
					} else if (!$allowclasssites && $allowpersonalsites) {
						if ($info['slot_type'] == 'personal')
							$sites[$name] =& $info;
		
					} else
						$sites[$name] =& $info;
				}
			}
		}
	
		if (count($sites)) {			
		//	printc("\n\t\t<tr>\n\t\t\t<td class='inlineth' colspan='2'>Sites to which you have editor permissions</td>\n\t\t</tr>");
			foreach (array_keys($sites) as $name)
				printSiteLine2($sites[$name]);			
		}
		unset($sites);		
	}
	
	
/******************************************************************************
 * copy site bar
 ******************************************************************************/
	printc("\n\t\t<tr>\n\t\t\t<td class='inlineth'>\n\t\t\t\t<form action='$PHP_SELF?$sid' method='post' name='copyform'>\n\t\t\t\t\t<table width='100%'>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>");
	
// ******************************* THESE TWO
// 	$allExistingSitesSlots = allSitesSlots($_SESSION[auser]);
// 	 = array_unique($allExistingSitesSlots[0]);
// 	$allExistingSlots = array_unique($allExistingSitesSlots[1]);
// ******************************* THESE TWO
	$allExistingSlots = array();
	$allExistingSites = array();
	
	//printpre($userOwnedSlots);
	
	if (is_array($userOwnedSlots)) {	
		foreach (array_keys($userOwnedSlots) as $name) {
			$info =& $userOwnedSlots[$name];
			if ($info['site_exits'])
				$allExistingSites[] = $name;
			else
				$allExistingSlots[] = $name;
		}
	}
	
	if (is_array($siteLevelEditorSites)) {
		foreach (array_keys($siteLevelEditorSites) as $name) {
			$info =& $siteLevelEditorSites[$name];
			$allExistingSites[] = $name;
		}
	}
	
	foreach (array_merge($usersCurrentClasses, $usersFutureClasses) as $name) {
		$info =& $usersAllClassesInfo[$name];
		if (!$info['site_exits']
			&& (!$info['slot_owner'] || $info['slot_owner'] == $_SESSION['auser'])
			&& ($_SESSION['atype'] == 'prof' || $_SESSION['atype'] == 'admin'))
		{
			$allExistingSlots[] = $name;
		}
	}
	
	$allExistingSites = array_unique($allExistingSites);
	natcasesort($allExistingSites);
	$allExistingSlots = array_unique($allExistingSlots);
	natcasesort($allExistingSlots);
	
	if (count($allExistingSites) && count($allExistingSlots)) {
			printc("Copy Site: ");
			printc("\n\t\t\t\t\t\t\t\t<select name='origname'>");
			printc("\n\t\t\t\t\t\t\t\t\t<option value=''>-choose-</option>");
			printOptions($allExistingSites);
			printc("\n\t\t\t\t\t\t\t\t</select>");
			printc(" to ");
			printc("\n\t\t\t\t\t\t\t\t<select name='newname'>");
			printc("\n\t\t\t\t\t\t\t\t\t<option value=''>-choose-</option>");
			printOptions($allExistingSlots);
			printc("\n\t\t\t\t\t\t\t\t</select>");
/*			printc(" Clear Permissions: <input type='checkbox' name='clearpermissions' value='1' checked='checked'/>"); */
			printc("\n\t\t\t\t\t\t\t\t Copy discussion posts: <input type='checkbox' name='copy_discussions' value='1' checked='checked'/>");
			printc("\n\t\t\t\t\t\t\t\t <input type='submit' name='copysite' value='Copy' class='button' />");
	}
	
	printc("\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td align='right'>");
	if ($_SESSION[amethod] =='db' || $_SESSION[lmethod]=='db') printc("<a href='passwd.php?$sid&amp;action=change' target='password' onclick='doWindow(\"password\",400,300)'>change password</a>");	
	printc("</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</form>\n\t\t\t</td>\n\t\t</tr>");
	
	printc("\n\t</table>");
	printc("\n</form>");
} else {
	//add_link(leftnav,"Home","index.php?$sid","","");
	//add_link(leftnav,"Personal Site List<br />","index.php?$sid&amp;action=list","","");
	add_link(leftnav,"Links");
	foreach ($defaultlinks as $t=>$u)
		add_link(leftnav,$t,"http://".$u,'','',"_blank");
//		add_link(leftnav,$t." <img src='globe.gif' border='0' align='absmiddle' height='15' width='15' />",$u,'','',"_blank");
	
	
	printc("\n\t<div class='title'>$defaulttitle</div>");
	printc("\n\t<div class='leftmargin'>");
	printc($defaultmessage);
	printc("\n\t</div>");
	
	// if this is the first time they have run Segue, we need to do some first-time
	// configuration
		if (!user::numDBUsers()) {
			require("_first_time_run.inc.php");
		}
		
}



/******************************************************************************
 * functions
 ******************************************************************************/


function printOptions($siteArray) {
	foreach ($siteArray as $n=>$site) {
		printc("\n<option value='$site'>$site</option>");
	}
}



/**
 * Build an array of all of the sites and slots that the user
 * is either the owner of or an editor (has permission add, edit, and delete) of
 */
function allSitesSlots ($user) {
	global $classes, $usersFutureClasses;
	$allsites = array();
	
	// The user's personal site
	if ($user == slot::getOwner($user) || !slot::exists($user)) {
		$allsites[$user] = array();
		$allsites[$user]['slot_name'] = $user;
		$allsites[$user]['slot_type'] = 'personal';
		$allsites[$user]['owner_uname'] = $user;
		$allsites[$user]['site_exits'] = false;
	}
	
	// Add slots that the user is an owner of.
	// This will include all of the created sites as well
	
	$allsites = array_merge($allsites, $slots);
	
	// Add the sites that the user is a Site-Level Editor for.
	$allsites =  array_merge($allsites, segue::getSiteInfoWhereUserIsSiteLevelEditor($user));
	
	
	$sitesEditorOf = segue::getSiteInfoWhereUserIsSiteLevelEditor($user);
	
	$usersAllClasses = array();
	if ($_SESSION[atype] == 'prof') {
		foreach ($classes as $n => $v) $usersAllClasses[] = $n;
		foreach ($usersFutureClasses as $n => $v) $usersAllClasses[] = $n;
	}
	
	printpre($allsites);
	printpre($usersAllClasses);
	printpre($sitesEditorOf);
	printpre($sitesOwnerOf);
	printpre($slots);
	$allsites = array_unique(array_merge($allsites,$usersAllClasses,$sitesOwnerOf,$sitesEditorOf,$slots));
	
	$allGroups = group::getGroupsOwnedBy($user);

	$sitesInGroups = array();
	foreach ($allGroups as $n=>$g) {
		$sitesInGroups = array_unique(array_merge($sitesInGroups,group::getClassesFromName($g)));
	}

	foreach ($allsites as $n=>$site) {
		if (!in_array($site,$sitesInGroups)) $allsites2[] = $site;
	}
	$allsites = array_merge($allsites2,$allGroups);
	asort($allsites);

/*	print "<pre>"; print_r($usersAllClasses); print "</pre>"; */
	$sites = array();
	$slots = array();
	foreach ($allsites as $n=>$site) {
		$siteObj =& new site($site);
		$exists = $siteObj->fetchFromDB();
		if ($exists)
			$sites[] = $site;
		else
			$slots[] = $site;
	}

	return array($sites, $slots);
}


// remove already printed sites from array of site objects
function &removePrinted(&$sites) {
	global $sitesprinted;
	$s = array();
	foreach (array_keys($sites) as $i => $key) {
		$site =& $sites[$key];
		$site_name = $site->name;
		if (!in_array($site_name,$sitesprinted)) $s[] =& $site;
	}
	return $s;
}

// prints one site
function printSiteLine2($siteInfo, $ed=0, $isclass=0, $atype='stud') {
	// The $ed parameter is a bunch of crap and makes assumptions about 
	// editor permissions that don't exist, such as profs of a class 
	// always being the owner. It should have no effect in this function.

	global $color,$possible_themes;
	global $sitesprinted;
	global $_full_uri;

	$name = $siteInfo['slot_name'];
//	printpre($siteInfo);
	

	if (in_array($name,$sitesprinted)) return;
	$sitesprinted[]=$name;

	$exists = $siteInfo['site_exists'];
//	printpre("exists:".$exists);


	$namelink = ($exists)?"$PHP_SELF?$sid&amp;action=site&amp;site=$name":"$PHP_SELF?$sid&amp;action=add_site&amp;sitename=$name";
	$namelink2 = ($exists)?"$PHP_SELF?$sid&amp;action=viewsite&amp;site=$name":"$PHP_SELF?$sid&amp;action=add_site&amp;sitename=$name";
	
	printc("\n\t\t<tr>");
	printc("\n\t\t\t<td class='td$color' colspan='2'>");
	$status = ($exists)?"Created":"Not Created";
	if ($exists) {
		if ($siteInfo['site_active']) 
			$active = "<span class='green'>active</span>";
		else
			$active = "<span class='red'>(inactive)</span>";
	}
	
	printc("\n\t\t\t\t<table width='100%' cellpadding='0' cellspacing='0'>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td align='left'>");
	
	if ($isclass 
		&& $_SESSION[atype] == 'prof' && ((!$exists 
			&& (!$siteInfo['slot_owner'] 
				|| $_SESSION[auser] == $siteInfo['slot_owner'])) 
			|| ($exists && $_SESSION[auser] == $siteInfo['slot_owner']))) 
	{
		// if:
		//		isclass - is a class
		//		if it doesn't exist, either there is no owner or we are the owner.
		//		if it exists, the user the owner
		printc("\n\t\t\t\t\t\t\t<input type='checkbox' name='group[]' value='$name' />");
	}
	
	printc("$name - ");	
	
	if ($exists) {
		printc("\n\t\t\t\t\t\t\t<span style ='font-size:14px;'><a href='$namelink'>".$siteInfo['site_title']."</a></span>");
	} else if (!$siteInfo['slot_owner'] || $_SESSION[auser] == $siteInfo['slot_owner']) {
	// if the slot doesn't have an owner or we are the owner.
		if ($_SESSION[atype] == 'prof' && $isclass) {
		//if ($isclass) {
			printc("\n\t\t\t\t\t\t\t<span style ='font-size:10px;'>");
			printc("Create: <a href='$namelink'>Site</a> ");
			printc("</span>");
		} else {
			printc("\n\t\t\t\t\t\t\t<span style ='font-size:10px;'><a href='$namelink'>Create Site</a></span>");		    
		}
	} else {
	// if the slot does have an owner that isn't us
		printc("\n\t\t\t\t\t\t\t<span style ='font-size:10px;'>This site is owned by user \"".$siteInfo['slot_owner']."\". Contact your system administrator if you feel you should own this site.</span>");
	
	}
	
	printc("\n\t\t\t\t\t\t</td>\n\t\t\t\t\t\t<td align='right'>");
	printc((($active)?"\n\t\t\t\t\t\t\t[$active]":""));
	printc("\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t</table>");
	//printc("<div style='padding-left: 20px;'>");
	
	
	// Class Group printing
	if ($siteInfo['is_classgroup']) {
		$classlist = group::getClassesFromName($name);
		$list = implode(", ",$classlist);
		printc("\n\t\t\t\t<div style='padding-left: 20px; font-size: 10px;'>this is a group and contains the following classes: <b>$list</b><br />\n\t\t\t\t</div>");
		$sitesprinted = array_merge($sitesprinted,$classlist);
	}
	if ($exists) {
		$addedby = $siteInfo['site_addedby'];
/*		$viewpermissions=$a[viewpermissions]; */
		$added = timestamp2usdate($siteInfo['site_added_timestamp']);
		$edited = $siteInfo['site_edited_timestamp'];
		$editedby = $siteInfo['site_editedby'];
		printc("\n\t\t\t\t<div style='padding-left: 20px; font-size: 10px;'>added by $addedby on $added".(($editedby)?", edited on ".timestamp2usdate($edited):"")."<br />\n\t\t\t\t</div>");
		
		if (!ereg("^0000", $siteInfo['activatedate']) || !ereg("^0000", $siteInfo['deactivatedate'])) {
			printc("\n\t\t\t\t<div style='padding-left: 20px; font-size: 10px;'>available: ");
			printc(txtdaterange($siteInfo['activatedate'], $siteInfo['deactivatedate']));
			printc("\n\t\t\t\t</div>");
		}

		printc("\n\t\t\t\t<div align='left'>");
	
		$addr = "$_full_uri/sites/$name";
		printc("\n\t\t\t\t\t<div style='padding-left: 20px; font-size: 12px;'>\n\t\t\t\t\t\tURL: <a href='$addr' target='_blank'>$addr</a><br />\n\t\t\t\t\t</div>\n\t\t\t\t</div>");
		
		printc("\n\t\t\t\t<div align='right'>");
		
		if ($_SESSION[auser] == $siteInfo['slot_owner'] 
			|| $siteInfo['hasPermissionDownA']
			|| $siteInfo['hasPermissionDownE']
			|| $siteInfo['hasPermissionDownD']) 
		{
			// if the user is an editor or the owner			
			printc("\n\t\t\t\t\t <a href='$PHP_SELF?$sid&amp;action=viewsite&amp;site=$name'>edit</a> | ");
		}
		
		if ($_SESSION[auser] == $siteInfo['slot_owner'] 
			|| ($siteInfo['hasSitePermissionA']
				&& $siteInfo['hasSitePermissionE']
				&& $siteInfo['hasSitePermissionD']))
		{
			// if the user is the owner or a site-level editor...
			printc("\n\t\t\t\t\t <a href='$PHP_SELF?$sid&amp;action=edit_site&amp;sitename=$name'>settings</a> | ");
		}
		
		if ($_SESSION[auser] == $siteInfo['slot_owner']) { 
			// if the user is the owner, not an editor
			printc("\n\t\t\t\t\t <a href='$PHP_SELF?$sid&amp;action=delete_site&amp;name=$name'>delete</a> | ");
			printc("\n\t\t\t\t\t <a href='edit_permissions.php?$sid&amp;site=$name' onclick='doWindow(\"permissions\",600,400)' target='permissions'>permissions</a>");
			
		} else if (($siteInfo['hasPermissionDownA']
				|| $siteInfo['hasPermissionDownE']
				|| $siteInfo['hasPermissionDownD'])
			&& $_SESSION[auser] != $siteInfo['slot_owner']) {	
			// if the user is an editor
			printc("\n\t\t\t\t\t <a href='edit_permissions.php?$sid&amp;site=$name' onclick='doWindow(\"permissions\",600,400)' target='permissions'>your permissions</a>");
		}
		if ($isclass  && $_SESSION[atype] == 'prof') {
			printc(" |\n\t\t\t\t\t <a href=\"Javascript:sendWindow('addstudents',500,400,'add_students.php?$sid&amp;name=".$name."')\">students</a> \n");
		}

		printc("\n\t\t\t\t</div>");
		
		
	}
		
	printc("\n\t\t\t</td>\n\t\t</tr>");
	
	$color=1-$color;
}

function printStudentSiteLine($className, $siteInfo) {
	global $studentSitesColor;
	if (!isset($studentSitesColor))
		$studentSitesColor=0;
						

	printc("\n\t\t\t\t<tr>\n\t\t\t\t\t<td class='td$studentSitesColor' width='150'>$className</td>");

	if ($siteInfo['site_exists']) {
		if ($siteInfo['site_active']) {
			printc("\n\t\t\t\t\t<td align='left' class='td$studentSitesColor'><a href='$PHP_SELF?$sid&amp;action=site&amp;site=".$siteInfo['slot_name']."'>".$siteInfo['site_title']."</a></td>");
					
		} else { 
			printc("\n\t\t\t\t\t<td style='color: #999' class='td$studentSitesColor'>created, not yet available</td>");
		}
		
		// check for an associated site slot and whether an associated site has been created for the current user
		$assoc_siteinfo = associatedSiteCreated($_SESSION[auser], $className);
		$assoc_site_title = $assoc_siteinfo['site_title'];

		if ($assoc_site_title != "") {
			 printSiteLine2($assoc_siteinfo, 0, 1);
			 $studentSitesColor = 1-$studentSitesColor;
		} else if (associatedSiteExists($_SESSION[auser], $className) == "true") {
			$studentSitesColor = 1-$studentSitesColor;
			printc("\n\t\t\t\t</tr><tr>\n\t\t\t\t\t<td class='td$studentSitesColor' width='150'>".$siteInfo['slot_name']."-".$_SESSION[auser]."</td>");
			printc("\n\t\t\t\t\t<td align='left' class='td$studentSitesColor'>Create: <a href='$PHP_SELF?$sid&amp;action=add_site&amp;sitename=".$siteInfo['slot_name']."-".$_SESSION[auser]."'> Site</a> (this site will be associated with span6695a-l07)</td>");

		}

				
	//check webcourses databases to see if course website was created in course folders (instead of Segue)
	} else if ($course_site = coursefoldersite($className)) {
		$course_url = urldecode($course_site['url']);
		$title = urldecode($course_site['title']);
		printc("\n\t\t\t\t\t<td style='color: #999' class='td$studentSitesColor'><a href='$course_url' target='new_window'>$title</td>");
	} else 
		printc("\n\t\t\t\t\t<td style='color: #999' class='td$studentSitesColor'>not created</td>");
	
	printc("\n\t\t\t\t</tr>");
	
	
	$studentSitesColor = 1-$studentSitesColor;
}

//$sitefooter .= "\n<div align='right' style='color: #999; font-size: 10px;'>by <a style='font-weight: normal; text-decoration: underline' href='mailto: gschineATmiddleburyDOTedu'>Gabriel Schine</a>, <a href='mailto:achapinATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Alex Chapin</a>, <a href='mailto:afrancoATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Adam Franco</a> and <a href='mailto:dradichkATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Dobo Radichkov</a></div>";
$_version = file_get_contents("version.txt");
$sitefooter .= "\n<div align='right' style='color: #999; font-size: 10px;'>
	Segue v.
	<a href='changelog/changelog.html' target='credits' onclick='doWindow(\"credits\",400,300);'>$_version</a>
	&copy;2007, Middlebury College: 
	<a href='credits.php' target='credits' onclick='doWindow(\"credits\",400,300);'>credits</a>
	</div>";

if ($debug && $printTimedQueries)
	print "\n<br/>Queries run in default.inc.php: ".($_totalQueries - $defaultStartQueries)."";

?>