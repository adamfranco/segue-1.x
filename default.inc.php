<?
// default page
$defaultStartQueries = $_totalQueries;

$pagetitle = "Segue";
$color = 0;
$sitesprinted=array();
//printpre($_SESSION);

if (isset($_REQUEST[expand_pastclasses])) {
	//printpre($_SESSION[expand_pastclasses]);
	$_SESSION[expand_pastclasses] = $_REQUEST[expand_pastclasses];
} else if (!$_SESSION[expand_pastclasses]) {
	$_SESSION[expand_pastclasses] = 0;
}


/******************************************************************************
 * public site listing link
 ******************************************************************************/
$leftnav_extra .= <<< END

<table width="100%" height="100%" border=0 cellpadding='0' cellspacing='0'>
	<tr>
		<td height="100%" valign="bottom" style="font-weight: bolder">
		<a href='sitelisting.php?$sid' onClick='doWindow("listing",600,500)' target='listing'>Site Listing</a>
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
	$query = "SELECT FK_site FROM slot WHERE slot_name = '$newname'";
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
	printc("<div align='right'><a href=email.php?$sid&action=user&from=home onClick='doWindow(\"email\",700,500)' target='email'>Your Posts</a></div>");	

	/*********************************************************
	 * Fetch all of the info for all of the sites and slots
	 * that the user is an editor or owner for, so we don't have
	 * to get them again.
	 *********************************************************/
	// this should include all sites that the user owns as well.
	$userOwnedSlots = slot::getSlotInfoWhereUserOwner($_SESSION['auser']);
	if (!array_key_exists($_SESSION['auser'], $userOwnedSlots)) {
		$userOwnedSlots[$_SESSION['auser']] = array();
		$userOwnedSlots[$_SESSION['auser']]['slot_name'] = $_SESSION['auser'];
		$userOwnedSlots[$_SESSION['auser']]['slot_type'] = 'personal';
		$userOwnedSlots[$_SESSION['auser']]['slot_owner'] = $_SESSION['auser'];
		$userOwnedSlots[$_SESSION['auser']]['site_exits'] = false;
	}
	
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
	
	$siteLevelEditorSites = segue::getSiteInfoWhereUserIsSiteLevelEditor($_SESSION['auser']);
	
	$anyLevelEditorSites = segue::getSiteInfoWhereUserIsEditor($_SESSION['auser']);
	
	
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
	
// 	print "classgroupLists = ";
// 	printpre($classgroupLists);
// 	print "siteLevelEditorSites = ";
// 	printpre($siteLevelEditorSites);
// 	print "anyLevelEditorSites = ";
// 	printpre($anyLevelEditorSites);
	
/*********************************************************
 * Class Sites for students
 *********************************************************/
	if ($allowclasssites) {
		$_class_list_titles = array("usersCurrentClasses"=>"Your Current Classes",
									"usersFutureClasses"=>"Upcoming Classes",
									"usersOldClasses"=>"Previous Semesters");
		
		// for students: print out list of classes
		if ($_SESSION[atype]=='stud') {
			printc("<table width=100%>");
			
			//loop through all classes in list
			foreach ($_class_list_titles as $timePeriod => $title) {
				
				if (count($$timePeriod)) {

					printc("<tr>");
					printc("<td valign=top>");

					/******************************************************************************
					 * expand/collapse link for previous sites listing
					 ******************************************************************************/		
					if ($timePeriod == "usersOldClasses") {
						
						if ($_SESSION[expand_pastclasses] == 0) {
							printc("<div class=title><a href=$PHP_SELF?expand_pastclasses=1>+</a> $title</div>");
							//printc("<a href=$PHP_SELF?expand_pastclasses=1>show</a>");
						} else {
							printc("<div class=title><a href=$PHP_SELF?expand_pastclasses=0>-</a> $title</div>");
						}
						
					// if not previous, then must be current classes...	
					} else {
						printc("<div class=title>$title</div>");
					}
					
					/******************************************************************************
					 * expand/collapse link for previous sites listing
					 ******************************************************************************/		
					if ($_SESSION[expand_pastclasses] == 0 && $timePeriod == "usersOldClasses") {
						// do nothing
					} else {																			
						printc("<table width=100%><tr><th>class</th><th>site</th></tr>");
						
						$groupsPrinted = array();
						foreach ($$timePeriod as $className) {
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
								printc("<tr><td colspan=2 style='background-color: red; font-weight: bold'>There was an error loading information for site: ".$classSiteName."</td></tr>");
						}
						
						printc("</tr></table>");
					}
					
					printc("</td>");
					printc("</tr>");
				}
			}
			printc("</td>");
			printc("</tr>");
			printc("</table>");
	
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
	

	printc("<div class='title'>Sites".helplink("sites")."</div>");
	
	printc("<form name=groupform action='$PHP_SELF?$sid&action=default' method=post>");
	
	printc("<table width=100%>");
	

/*********************************************************
 * Personal Sites
 *********************************************************/
	if ($allowpersonalsites) {
		// print out the personal site if there is a slot for them that they own.
		if ($userOwnedSlots[$_SESSION['auser']]['slot_owner'] == $_SESSION['auser']) {
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
			} else {
				printc("<tr><td class='inlineth' colspan=2>Personal Site</td></tr>");
				printSiteLine2($userOwnedSlots[$_SESSION['auser']]);
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
				printc("<tr><td class='inlineth' colspan=2>Current Class Sites</td></tr>");
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
						printc("<tr><td colspan=2 style='background-color: red; font-weight: bold'>There was an error loading information for site: ".$classSiteName."</td></tr>");
				}
			}

			//upcoming classes
			if (count($usersFutureClasses)) {		    
				printc("<tr><td class='inlineth' colspan=2>Upcoming Classes</td></tr>");
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
						printc("<tr><td colspan=2>There was an error loading information for site: ".$classSiteName."</td></tr>");
				}
			}
			
			//info/interface for groups
			printc("<tr><th colspan=2 align='right'>add checked sites to group: <input type='text' name=newgroup size=10 class=textfield>");
			$havegroups = count($userOwnedGroups);
			if ($havegroups) {
				printc(" <select name='groupname' onChange='document.groupform.newgroup.value = document.groupform.groupname.value'>");
				printc("<option value=''>-choose-");
				foreach ($userOwnedGroups as $group) {
					printc("<option value='$group'>$group\n");
				}
				printc("</select>");
			}
			printc(" <input type=submit class=button value='add'>");
			printc("</th></tr>");
			printc("<tr><th colspan=2 align='left'>");
			printc("<div style='padding-left: 10px; font-size: 10px;'>By adding sites to a group you can consolidate multiple class sites into one entity. This is useful if you teach multiple sections of the same class and want to work on only one site for those classes/sections. Check the boxes next to the classes you would like to add, and either type in a new group name or choose an existing one.");
			if ($havegroups) printc("<div class=desc><a href='edit_groups.php?$sid' target='groupeditor' onClick='doWindow(\"groupeditor\",400,400)'>[edit class groups]</a></div>");
			printc("</th></tr>");
				
		}
	}

	
/******************************************************************************
 * sites where the user is an Editor
 ******************************************************************************/
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
		printc("<tr><td class='inlineth' colspan=2>Sites to which you have editor permissions</td></tr>");
		foreach (array_keys($sites) as $name)
			printSiteLine2($sites[$name]);
	}
	unset($sites);
	
	
/*********************************************************
 * Other sites where user is owner
 *********************************************************/
	$sites=array();
	foreach (array_keys($userOwnedSlots) as $name) {
		$info =& $userOwnedSlots[$name];
		
		if (!in_array($name, $sitesprinted)) {
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
	
	if (count($sites)) {
		printc("<tr><td class='inlineth' colspan=2>");
		
		printc ("Other Sites".helplink("othersites","What are these?")."</td></tr>");
			foreach (array_keys($sites) as $name)
				printSiteLine2($sites[$name]);
	}
	unset($sites);
	
	
/******************************************************************************
 * copy site bar
 ******************************************************************************/
	printc("<tr><td class='inlineth'><form action=$PHP_SELF?$sid method=post name='copyform'><table width=100%><tr><td>");
	
// ******************************* THESE TWO
// 	$allExistingSitesSlots = allSitesSlots($_SESSION[auser]);
// 	 = array_unique($allExistingSitesSlots[0]);
// 	$allExistingSlots = array_unique($allExistingSitesSlots[1]);
// ******************************* THESE TWO
	$allExistingSlots = array();
	$allExistingSites = array();
	
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
			printc("<select name='origname'>");
			printc("<option value=''>-choose-\n");
			printOptions($allExistingSites);
			printc("</select>");
			printc(" to ");
			printc("<select name='newname'>");
			printc("<option value=''>-choose-\n");
			printOptions($allExistingSlots);
			printc("</select>");
/*			printc(" Clear Permissions: <input type=checkbox name='clearpermissions' value='1' checked>"); */
			printc(" Copy discussion posts: <input type=checkbox name='copy_discussions' value='1' checked>");
			printc(" <input type=submit name='copysite' value='Copy' class='button'></form>");
	}
	
	printc("</td><td align='right'>");
	if ($_SESSION[amethod] =='db' || $_SESSION[lmethod]=='db') printc("<a href='passwd.php?$sid&action=change' target='password' onClick='doWindow(\"password\",400,300)'>change password</a>");	
	printc("</td></tr></table></td></tr>");
	
	printc("</table>");
} else {
	//add_link(leftnav,"Home","index.php?$sid","","");
	//add_link(leftnav,"Personal Site List<br />","index.php?$sid&action=list","","");
	add_link(leftnav,"Links");
	foreach ($defaultlinks as $t=>$u)
		add_link(leftnav,$t,"http://".$u,'','',"_blank");
//		add_link(leftnav,$t." <img src=globe.gif border=0 align=absmiddle height=15 width=15>",$u,'','',"_blank");
	
	
	printc("<div class=title>$defaulttitle</div>");
	printc("<div class=leftmargin>");
	printc($defaultmessage);
	
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
		printc("<option value='$site'>$site\n");
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
	

	if (in_array($name,$sitesprinted)) return;
	$sitesprinted[]=$name;

	$exists = $siteInfo['site_exits'];
	


	$namelink = ($exists)?"$PHP_SELF?$sid&action=site&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
	$namelink2 = ($exists)?"$PHP_SELF?$sid&action=viewsite&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
	
	printc("<tr>");
	printc("<td class=td$color colspan=2>");
	$status = ($exists)?"Created":"Not Created";
	if ($exists) {
		if ($siteInfo['site_active']) 
			$active = "<span class=green>active</span>";
		else
			$active = "<span class=red>(inactive)</span>";
	}
	
	printc("<table width=100% cellpadding='0' cellspacing='0'><tr><td align='left'>");
	
	if ($isclass 
		&& ((!$exists 
			&& (!$siteInfo['slot_owner'] 
				|| $_SESSION[auser] == $siteInfo['slot_owner'])) 
			|| ($exists && $_SESSION[auser] == $siteInfo['slot_owner']))) 
	{
		// if:
		//		isclass - is a class
		//		if it doesn't exist, either there is no owner or we are the owner.
		//		if it exists, the user the owner
		printc("<input type=checkbox name='group[]' value='$name'>");
	}
	
	printc("$name - ");	
	
	if ($exists) {
		printc("<span style ='font-size:14px;'><a href='$namelink'>".$siteInfo['site_title']."</a></span>");
	} else if (!$siteInfo['slot_owner'] || $_SESSION[auser] == $siteInfo['slot_owner']) {
	// if the slot doesn't have an owner or we are the owner.
		if ($_SESSION[atype] == 'prof' && $isclass) {
			printc("<span style ='font-size:10px;'>");
			printc("Create: <a href='$namelink'>Site</a> ");
			printc("</span>");
		} else {
			printc("<span style ='font-size:10px;'><a href='$namelink'>Create Site</a></span>");		    
		}
	} else {
	// if the slot does have an owner that isn't us
		printc("<span style ='font-size:10px;'>This site is owned by user \"".$siteInfo['slot_owner']."\". Contact your system administrator if you feel you should own this site.</span>");
	
	}
	
	printc("</td><td align='right'>");
	printc((($active)?"[$active]":""));
	printc("</td></tr></table>");
	//printc("<div style='padding-left: 20px;'>");
	
	
	// Class Group printing
	if ($siteInfo['is_classgroup']) {
		$classlist = group::getClassesFromName($name);
		$list = implode(", ",$classlist);
		printc("<div style='padding-left: 20px; font-size: 10px;'>this is a group and contains the following classes: <b>$list</b><br /></div>");
		$sitesprinted = array_merge($sitesprinted,$classlist);
	}
	if ($exists) {
		$addedby = $siteInfo['site_addedby'];
/*		$viewpermissions=$a[viewpermissions]; */
		$added = timestamp2usdate($siteInfo['site_added_timestamp']);
		$edited = $siteInfo['site_edited_timestamp'];
		$editedby = $siteInfo['site_editedby'];
		printc("<div style='padding-left: 20px; font-size: 10px;'>added by $addedby on $added".(($editedby)?", edited on ".timestamp2usdate($edited):"")."<br /></div>");
		
		if (!ereg("^0000", $siteInfo['activatedate']) || !ereg("^0000", $siteInfo['deactivatedate'])) {
			printc("<div style='padding-left: 20px; font-size: 10px;'>available: ");
			printc(txtdaterange($siteInfo['activatedate'], $siteInfo['deactivatedate']));
			printc("</div>");
		}

		printc("<div align='left'>");
	
		$addr = "$_full_uri/sites/$name";
		printc("<div style='padding-left: 20px; font-size: 12px;'>URL: <a href='$addr' target='_blank'>$addr</a><br /></div></div>");
		
		printc("<div align='right'>");
		
		if ($_SESSION[auser] == $siteInfo['slot_owner'] 
			|| $siteInfo['hasPermissionDownA']
			|| $siteInfo['hasPermissionDownE']
			|| $siteInfo['hasPermissionDownD']) 
		{
			// if the user is an editor or the owner			
			printc(" <a href='$PHP_SELF?$sid&action=viewsite&site=$name'>edit</a> | ");
		}
		
		if ($_SESSION[auser] == $siteInfo['slot_owner'] 
			|| ($siteInfo['hasSitePermissionA']
				&& $siteInfo['hasSitePermissionE']
				&& $siteInfo['hasSitePermissionD']))
		{
			// if the user is the owner or a site-level editor...
			printc(" <a href='$PHP_SELF?$sid&action=edit_site&sitename=$name'>settings</a> | ");
		}
		
		if ($_SESSION[auser] == $siteInfo['slot_owner']) { 
			// if the user is the owner, not an editor
			printc(" <a href='$PHP_SELF?$sid&action=delete_site&name=$name'>delete</a> | ");
			printc(" <a href='edit_permissions.php?$sid&site=$name' onClick='doWindow(\"permissions\",600,400)' target='permissions'>permissions</a>");
			
		} else if (($siteInfo['hasPermissionDownA']
				|| $siteInfo['hasPermissionDownE']
				|| $siteInfo['hasPermissionDownD'])
			&& $_SESSION[auser] != $siteInfo['slot_owner']) {	
			// if the user is an editor
			printc(" <a href='edit_permissions.php?$sid&site=$name' onClick='doWindow(\"permissions\",600,400)' target='permissions'>your permissions</a>");
		}
		if ($isclass) {
			printc(" | <a href=\"Javascript:sendWindow('addstudents',500,400,'add_students.php?$sid&name=".$name."')\">students</a> \n");
		}

		printc("</div>");
		
		
	}
	
	
	printc("</div>");
	
	printc("</td></tr>");
	
	$color=1-$color;
}

function printStudentSiteLine($className, $siteInfo) {
	global $studentSitesColor;
	if (!isset($studentSitesColor))
		$studentSitesColor=0;
						

	printc("<tr><td class=td$studentSitesColor width= 150>$className</td>");

	if ($siteInfo['site_exits']) {
		if ($siteInfo['site_active']) 
			printc("<td align='left' class=td$studentSitesColor><a href='$PHP_SELF?$sid&action=site&site=".$siteInfo['slot_name']."'>".$siteInfo['site_title']."</a></td>");
		else 
			printc("<td style='color: #999' class=td$studentSitesColor>created, not yet available</td>");
	
	//check webcourses databases to see if course website was created in course folders (instead of Segue)
	} else if ($course_site = coursefoldersite($className)) {
		$course_url = urldecode($course_site['url']);
		$title = urldecode($course_site['title']);
		printc("<td style='color: #999' class=td$studentSitesColor><a href='$course_url' target='new_window'>$title</td>");
	} else 
		printc("<td style='color: #999' class=td$studentSitesColor>not created</td>");
	
	printc("</tr>");
	
	
	$studentSitesColor = 1-$studentSitesColor;
}

//$sitefooter .= "<div align='right' style='color: #999; font-size: 10px;'>by <a style='font-weight: normal; text-decoration: underline' href='mailto: gschineATmiddleburyDOTedu'>Gabriel Schine</a>, <a href='mailto:achapinATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Alex Chapin</a>, <a href='mailto:afrancoATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Adam Franco</a> and <a href='mailto:dradichkATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Dobo Radichkov</a></div>";
$_version = file_get_contents("version.txt");
$sitefooter .= "<div align='right' style='color: #999; font-size: 10px;'>
	Segue v.
	<a href='changelog/changelog.html' target='credits' onClick='doWindow(\"credits\",400,300);'>$_version</a>
	&copy;2004, Middlebury College: 
	<a href='credits.php' target='credits' onClick='doWindow(\"credits\",400,300);'>credits</a>
	</div>";

if ($debug && $printTimedQueries)
	print "\n<br/>Queries run in default.inc.php: ".($_totalQueries - $defaultStartQueries)."";

?>