<? /* $Id$ */

// default page

$pagetitle = "Segue";

$color = 0;
$sitesprinted=array();

/******************************************************************************
 * public site listing link
 ******************************************************************************/
$leftnav_extra .= <<< END
<br/><br/>
<table width="100%" height="100%" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td height="100%" valign="bottom" style="font-weight: bolder">
		<a href='sitelisting.php?$sid' onClick='doWindow("listing",600,500)' target='listing'>Public Site Listing</a>
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
/*	print "Move: $origname to $newname  <br> <pre>"; */
/*	print_r($origSite); */
/*	print "</pre>"; */
/* $origSite->copySite($newname,$clearpermissions); */

	/******************************************************************************
	 * Check to make sure that the slot is not already in use.
	 * Hitting refresh after copying a site, will insert a second copy of the site
	 * if we don't check for this.
	 ******************************************************************************/
	$query = "SELECT FK_site FROM slot WHERE slot_name = '$newname'";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	if (!$a[FK_site]) {
		$origSite->copySite($newname);
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

	add_link(leftnav,"Links");
	foreach ($defaultlinks as $t=>$u)
		add_link(leftnav,$t,"http://".$u,'','',"_blank");
	
	add_link(leftnav,helplink("index"));

	/******************************************************************************
	 * List sites
	 ******************************************************************************/
	if ($allowclasssites) {
		$_class_list_titles = array("classes"=>"Your Current Classes","futureclasses"=>"Upcoming Classes","oldclasses"=>"Previous Semester");
		
		// for students: print out list of classes
		if ($_SESSION[atype]=='stud') {
			printc("<table width=100%>");
			
			foreach ($_class_list_titles as $var=>$t) {

				if (count($$var)) {
					//print out current classes
					printc("<tr>");
					printc("<td valign=top>");		  
					printc("<div class=title>$t</div>");
					//print class list
					//printclasses($classes);
					
									
					printc("<table width=100%><tr><th>class</th><th>site</th></tr>");
					$c=0;

					foreach (array_keys($$var) as $cl) {
					
						printc("<tr><td class=td$c width= 150>$cl</td>");
						$site =& new site($cl);

						if (($gr = inclassgroup($cl)) || ($site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,0,true))) {
							if ($gr) { $site =& new site($gr); $site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,0,true); }
							if ($site->canview()) printc("<td align=left class=td$c><a href='$PHP_SELF?$sid&action=site&site=".$site->name."'>".$site->getField("title")."</a></td>");
							else printc("<td style='color: #999' class=td$c>created, not yet available</td>");
							//check webcourses databases to see if course website was created in course folders (instead of Segue)
						} else if ($course_site = coursefoldersite($cl)) {					  
							$course_url = urldecode($course_site['url']);
							$title = urldecode($course_site['title']);
							printc("<td style='color: #999' class=td$c><a href='$course_url' target='new_window'>$title</td>");
							db_connect($dbhost, $dbuser, $dbpass, $dbdb);
						} else printc("<td style='color: #999' class=td$c>not created</td>");
						printc("</tr>");
						$c = 1-$c;
					}
					printc("</tr></table>");
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

	if ($allowpersonalsites) {
		// print out the personal site
		if ($_SESSION[auser] == slot::getOwner($_SESSION['auser']) || !slot::exists($_SESSION['auser'])) {
			printc("<tr><td class='inlineth' colspan=2>Personal Site</td></tr>");
			printSiteLine(new site($_SESSION[auser]));
		}
	}
	
	if ($allowclasssites) {	       
		//class sites for professors (for student see above)
		if ($_SESSION[atype] == 'prof' || $_SESSION[atype] == 'admin') {
			//current classes
			if (count($classes)) {
				printc("<tr><td class='inlineth' colspan=2>Current Class Sites</td></tr>");
				$gs = array();
				foreach ($classes as $c=>$a) {
					if ($g = group::getNameFromClass($c)) {
						if (!$gs[$g]) printSiteLine(new site($g),0,1,$_SESSION[atype]);
						$gs[$g] = 1;
					} else
						printSiteLine(new site($c),0,1,$_SESSION[atype]);
				}
			}
			//upcoming classes
			if (count($futureclasses)) {		    
				printc("<tr><td class='inlineth' colspan=2>Upcoming Classes</td></tr>");
				$gs = array();
				foreach ($futureclasses as $c=>$a) {
					if ($g = group::getNameFromClass($c)) {
						if (!$gs[$g]) printSiteLine(new site($g));
						$gs[$g] = 1;
					} else
						printSiteLine(new site($c),0,1);
				}
			}
			
			//info/interface for groups
			printc("<tr><th colspan=2 align=right>add checked sites to group: <input type=text name=newgroup size=10 class=textfield>");
			$havegroups = count(($grs = group::getGroupsOwnedBy($_SESSION[auser])));
			if ($havegroups) {
				printc(" <select name='groupname' onChange='document.groupform.newgroup.value = document.groupform.groupname.value'>");
				printc("<option value=''>-choose-");
				foreach ($grs as $g) {
					printc("<option value='$g'>$g\n");
				}
				printc("</select>");
			}
			printc(" <input type=submit class=button value='add'>");
			printc("</th></tr>");
			printc("<tr><th colspan=2 align=left>");
			printc("<div style='padding-left: 10px; font-size: 10px;'>By adding sites to a group you can consolidate multiple class sites into one entity. This is useful if you teach multiple sections of the same class and want to work on only one site for those classes/sections. Check the boxes next to the classes you would like to add, and either type in a new group name or choose an existing one.");
			if ($havegroups) printc("<div class=desc><a href='edit_groups.php?$sid' target='groupeditor' onClick='doWindow(\"groupeditor\",400,400)'>[edit class groups]</a></div>");
			printc("</th></tr>");
				
		}
	}

	
/******************************************************************************
 * output a list of the user's other sites
 ******************************************************************************/

	$sites = array();
	$esites = segue::buildObjArrayFromSites(segue::getAllSitesWhereUserIsEditor());
	foreach ($esites as $n=>$s) {
		if (!in_array($n,$sitesprinted) && $s->hasPermissionDown("add or edit or delete",$_SESSION[auser],0,1) && $_SESSION[auser] != slot::getOwner($s->name)) {
			if ($allowclasssites && !$allowpersonalsites && $s->getField("type")!='personal')
				$sites[] =& $esites[$n];
			else if (!$allowclasssites && $allowpersonalsites && $s->getField("type")=='personal')
				$sites[] =& $esites[$n];
			else
				$sites[] =& $esites[$n];
		}
	}

	// if they are editors for any sites, they will be in the $sites[] array
/*	   print "<pre>"; */
/*	   print_r($sites); */
/*	   print_r($esites); */
/*	   print "</pre>"; */

	if (count($sites)) {
		printc("<tr><td class='inlineth' colspan=2>Sites to which you have editor permissions</td></tr>");
		foreach (array_keys($sites) as $i=>$n)
			printSiteLine($sites[$n],1);
	}

	$sites=array();
	$esites=segue::buildObjArrayFromSites($all_sites); // variable $s was initiated in index.php and is equal to segue::getAllSites($_SESSION[auser])
	foreach ($esites as $n=>$s) {
		if ($allowclasssites && !$allowpersonalsites && $s->getField("type")!='personal')
			$sites[] =& $esites[$n];
		else if (!$allowclasssites && $allowpersonalsites && $s->getField("type")=='personal')
			$sites[] =& $esites[$n];
		else
			$sites[] =& $esites[$n];
	}

 /******************************************************************************
 * remove sites & slots if $allowclasssites or $allowpersonalsites is disabled.
 ******************************************************************************/
	$slots_ = slot::getAllSlots($_SESSION[auser]);
	$slots = array();
/*	print_r($slots); */

	if (!$allowclasssites || !$allowpersonalsites) {
		$sites2 = array();
		foreach (array_keys($sites) as  $i=>$n) {
			$siteObj =& $sites[$n];
			if (!$allowclasssites) {
				if ($siteObj->getfield("type") == "personal") $sites2[] =& $siteObj;
			}
			if (!$allowpersonalsites) {
				if ($siteObj->getfield("type") != "personal") $sites2[] =& $siteObj;
			}
		}

		$sites = array();
		foreach (array_keys($sites2) as $i=>$n)
			$sites[] =& $sites2[$n];
		
		$slots2 = array();

		foreach ($slots_ as  $s) {
			$slotObj =& new slot($s);
			if (!$allowclasssites) {
				if ($slotObj->getfield("type") == "personal") $slots2[] =& new site($s);
			}
			if (!$allowpersonalsites) {
				if ($slotObj->getfield("type") != "personal") $slots2[] =& new site($s);
			}
		}

		$slots = array();
		foreach (array_keys($slots2) as $i=>$n)
			$slots[] =& $slots2[$n];
	} 
	else
		foreach($slots_ as $n)
			$slots[] =& new site($n);
			
			
	$da_sites = array();
	foreach (array_keys($sites) as $i=>$n)
		$da_sites[] =& $sites[$n];
	foreach (array_keys($slots) as $i=>$n)
		$da_sites[] =& $slots[$n];

	$sites =& removePrinted($da_sites);
	
	if (count($sites)) {
		printc("<tr><td class='inlineth' colspan=2>Other Sites".helplink("othersites","What are these?")."</td></tr>");
		foreach (array_keys($sites) as $i=>$n)
			printSiteLine($sites[$n]);
	}
	
/******************************************************************************
 * copy site bar
 ******************************************************************************/
	printc("<tr><td class='inlineth'><form action=$PHP_SELF?$sid method=post name='copyform'><table width=100%><tr><td>");
	
// ******************************* THESE TWO
	$allExistingSitesSlots = allSitesSlots($_SESSION[auser]);
	$allExistingSites = $allExistingSitesSlots[0];
	$allExistingSlots = $allExistingSitesSlots[1];
// ******************************* THESE TWO
	
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
			printc(" <input type=submit name='copysite' value='Copy' class='button'></form>");
	}
	
	printc("</td><td align=right>");
	if ($_SESSION[amethod] =='db' || $_SESSION[lmethod]=='db') printc("<a href='passwd.php?' target='password' onClick='doWindow(\"password\",400,300)'>change password</a>");	
	printc("</td></tr></table></td></tr>");
	
	printc("</table>");
} else {
	//add_link(leftnav,"Home","index.php?$sid","","");
	//add_link(leftnav,"Personal Site List<br>","index.php?$sid&action=list","","");
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


function allSitesSlots ($user) {
	global $classes, $futureclasses;
	$allsites = array();
	if ($user == slot::getOwner($user) || !slot::exists($user)) $allsites[] = $user;

	$sitesOwnerOf = segue::getAllSites($user);
	$slots = slot::getAllSlots($user);
	$sitesEditorOf = array();
	$esites = segue::buildObjArrayFromSites(segue::getAllSitesWhereUserIsEditor($user));

	foreach ($esites as $o) {
			if ($o->hasPermission("add and edit and delete",$user)) $sitesEditorOf[] = $o->name;
	}
	$allclasses = array();
	if ($_SESSION[atype] == 'prof') {
		foreach ($classes as $n => $v) $allclasses[] = $n;
		foreach ($futureclasses as $n => $v) $allclasses[] = $n;
	}

	$allsites = array_unique(array_merge($allsites,$allclasses,$sitesOwnerOf,$sitesEditorOf,$slots));
	
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

/*	print "<pre>"; print_r($allclasses); print "</pre>"; */
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
function & removePrinted(& $sites) {
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
function printSiteLine(& $site,$ed=0,$isclass=0,$atype='stud') {
	// The $ed parameter is a bunch of crap and makes assumptions about 
	// editor permissions that don't exist, such as profs of a class 
	// always being the owner. It should have no effect in this function.

	global $color,$possible_themes;
	global $sitesprinted;
	global $_full_uri;

	$obj =& $site;
	$name = $obj->getField("name");

	if (in_array($name,$sitesprinted)) return;
	$sitesprinted[]=$name;

	$isgroup = ($classlist = group::getClassesFromName($name))?1:0;

	$exists = $obj->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,0,true);
	
/*	print "<pre>"; */
/*	print_r($obj); */
/*	print "</pre>"; */

	$namelink = ($exists)?"$PHP_SELF?$sid&action=site&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
	$namelink2 = ($exists)?"$PHP_SELF?$sid&action=viewsite&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
/*	if ($exists) $a = db_get_line("sites","name='$name'"); */
	
	printc("<tr>");
	printc("<td class=td$color colspan=2>");
	$status = ($exists)?"Created":"Not Created";
	if ($exists) {
		if ($obj->canview("anyuser")) $active = "<span class=green>active</span>";
		else $active = "<span class=red>(inactive)</span>";
	}
	printc("<table width=100% cellpadding=0 cellspacing=0><tr><td align=left>");
	
	if ($isclass && ((!$exists && (!slot::getOwner($obj->name) || $_SESSION[auser] == slot::getOwner($obj->name))) || ($exists && $_SESSION[auser] == slot::getOwner($obj->name)))) {
		// if:
		//		isclass - is a class
		//		if it doesn't exist, either there is no owner or we are the owner.
		//		if it exists, the user the owner
		printc("<input type=checkbox name='group[]' value='$name'>");
	}
	
	printc("$name - ");
	
	
	//printc("<td align=right style='font-size: 11px; color: #777;'>");
	if ($exists) {
		printc("<span style ='font-size:14px;'><a href='$namelink'>".$obj->getField("title")."</a></span>");
	} else if (!slot::getOwner($obj->name) || $_SESSION[auser] == slot::getOwner($obj->name)) {
	// if the slot doesn't have an owner or we are the owner.
		if ($_SESSION[atype] == 'prof' && $isclass) {
			printc("<span style ='font-size:10px;'>Create: <a href='$namelink'>Site</a></span>");
		} else {
			printc("<span style ='font-size:10px;'><a href='$namelink'>Create Site</a></span>");		    
		}
	} else {
	// if the slot does have an owner that isn't us
		printc("<span style ='font-size:10px;'>This site is owned by user \"".slot::getOwner($obj->name)."\". Contact your system administrator if you feel you should own this site.</span>");
	
	}
	
	printc("</td><td align=right>");
	printc((($active)?"[$active]":""));
	printc("</td></tr></table>");
	//printc("<div style='padding-left: 20px;'>");
	
	
	if ($isgroup) {
		$list = implode(", ",$classlist);
		printc("<div style='padding-left: 20px; font-size: 10px;'>this is a group and contains the following classes: <b>$list</b><br></div>");
	}
	if ($exists) {
		$addedby = $obj->getField("addedby");
/*		$viewpermissions=$a[viewpermissions]; */
		$added = timestamp2usdate($obj->getField("addedtimestamp"));
		$edited = $obj->getField("editedtimestamp");
		$editedby = $obj->getField("editedby");
		printc("<div style='padding-left: 20px; font-size: 10px;'>added by $addedby on $added".(($editedby)?", edited on ".timestamp2usdate($edited):"")."<br></div>");
		
		if ($obj->getField("activatedate") != '0000-00-00' || $obj->getField("deactivatedate") != '0000-00-00') {
			printc("<div style='padding-left: 20px; font-size: 10px;'>available: ");
			printc(txtdaterange($obj->getField("activatedate"),$obj->getField("deactivatedate")));
/*			if ($viewpermissions != 'anyone') { */
/*				printc(" to "); */
/*				if ($viewpermissions == 'midd') printc("$cfg[inst_name] users"); */
/*				if ($viewpermissions == 'class') printc("students in this class"); */
/*				 */
/*			} */
			printc("</div>");
		}

		printc("<div align=left>");
	
		$addr = "$_full_uri/sites/$name";
		printc("<div style='padding-left: 20px; font-size: 12px;'>URL: <a href='$addr' target='_blank'>$addr</a><br></div></div>");
		
		printc("<div align=right>");
		
		if ($_SESSION[auser] == slot::getOwner($obj->name) || $obj->isEditor($_SESSION[auser])) {
			// if the user is an editor or the owner			
			printc(" <a href='$PHP_SELF?$sid&action=viewsite&site=$name'>edit</a> | ");
		}
		
		if ($obj->hasPermission("edit")) {
			// if the user is the owner or a site-level editor...
			printc(" <a href='$PHP_SELF?$sid&action=edit_site&sitename=$name'>settings</a> | ");
		}
		
		if ($_SESSION[auser] == slot::getOwner($obj->name)) { 
			// if the user is the owner, not an editor
			printc(" <a href='$PHP_SELF?$sid&action=delete_site&name=$name'>delete</a> | ");
			printc(" <a href='edit_permissions.php?$sid&site=$name' onClick='doWindow(\"permissions\",600,400)' target='permissions'>permissions</a>");
			
		} else if ($obj->hasPermissionDown("add or edit or delete",$_SESSION[auser],0,1) && $_SESSION[auser] != slot::getOwner($obj->name)) {	
			// if the user is an editor
			printc(" <a href='edit_permissions.php?$sid&site=$name' onClick='doWindow(\"permissions\",600,400)' target='permissions'>your permissions</a>");
		}
		printc("</div>");
		
		
	}
	
	
	printc("</div>");
	
	printc("</td></tr>");
	
	$color=1-$color;
}

//$sitefooter .= "<div align=right style='color: #999; font-size: 10px;'>by <a style='font-weight: normal; text-decoration: underline' href='mailto: gschineATmiddleburyDOTedu'>Gabriel Schine</a>, <a href='mailto:achapinATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Alex Chapin</a>, <a href='mailto:afrancoATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Adam Franco</a> and <a href='mailto:dradichkATmiddleburyDOTedu' style='font-weight: normal; text-decoration: underline'>Dobo Radichkov</a></div>";
$_version = file_get_contents("version.txt");
$sitefooter .= "<div align=right style='color: #999; font-size: 10px;'>Segue v.$_version &copy;2003, Middlebury College: <a href='credits.php' target='credits' onClick='doWindow(\"credits\",400,300);'>credits</a></div>";?>