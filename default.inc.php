<? // default.inc.php
	// this is a test default script

if ($settings) session_unregister("settings");

$pagetitle = "Segue";

$color = 0;
$sitesprinted=array();

if ($allowclasssites != $allowpersonalsites && ($personalsitesurl || $classsitesurl)) {
	if ($allowclasssites) {
		add_link(topnav,"Classes");
		add_link(topnav,"Community","$personalsitesurl",'','','');
	} else {
		add_link(topnav,"Classes","$classsitesurl",'','','');
		add_link(topnav,"Community");
	}
}


if ($_loggedin) {
	// -------------------------------------------------------------------
	//add_link(leftnav,"Home","index.php?$sid","","");
	//add_link(leftnav,"Personal Site List<br>","index.php?$sid&action=list","","");
	add_link(leftnav,"Links");
	foreach ($defaultlinks as $t=>$u)
		add_link(leftnav,$t,"http://".$u,'','',"_blank");
	
     /* -------------------- list of sites -------------------- */	
	if ($allowclasssites) {	
		// for students: print out list of classes
		if ($atype=='stud') {
			printc("<table width=100%>");
			
			if (count($classes)) {
				//print out current classes
				printc("<tr>");
				printc("<td valign=top>");		
				printc("<div class=title>Your Current Classes</div>");
				//print class list
				printc("<table width=100%><tr><th>class</th><th>site</th></tr>");
				$c=0;
				foreach (array_keys($classes) as $cl) {
				
					printc("<tr><td class=td$c width= 150>$cl</td>");
					if (($gr = inclassgroup($cl)) || ($db = db_get_line("sites","name='$cl'"))) {
						if ($gr) $db=db_get_line("sites","name='$gr'");
						if (canview($db)) printc("<td align=left class=td$c><a href='$PHP_SELF?$sid&action=site&site=".$db[name]."'>".$db[title]."</a></td>");
						else printc("<td style='color: #999' class=td$c>created, not yet available</td>");
						
					//check webcourses databases to see if course website was created in course folders (instead of SitesDB)
					} else if ($course_site = coursefoldersite($cl)) {					
						$course_url = urldecode($course_site['url']);
						$title = urldecode($course_site['title']);
						printc("<td style='color: #999' class=td$c><a href='$course_url' target='new_window'>$title</td>");
						db_connect($dbhost, $dbuser, $dbpass, $dbdb);
										
					} else
						printc("<td style='color: #999' class=td$c>not created</td>");
						printc("</tr>");
						$c = 1-$c;
						db_connect($dbhost, $dbuser, $dbpass, $dbdb);
				}
				printc("</tr></table>");
				printc("</td>");
				printc("</tr>");
	
			}
			
			//print out student classes for previous semester
			if (count($oldclasses)) {
				printc("<tr>");
				printc("<td valign=top>");		
				printc("<div class=title>Previous Semester</div>");
		//		print "debug: count(oldclasses) = ".count($oldclasses)."<BR>";//debug
				//print class list
				printc("<table width=100%><tr><th>class</th><th>site</th></tr>");
				$c=0;
				foreach (array_keys($oldclasses) as $cl) {
					printc("<tr><td class=td$c width= 150>$cl</td>");
					if (($gr = inclassgroup($cl)) || ($db = db_get_line("sites","name='$cl'"))) {
						if ($gr) $db=db_get_line("sites","name='$gr'");
						if (canview($db)) printc("<td align=left class=td$c><a href='$PHP_SELF?$sid&action=site&site=".$db[name]."'>".$db[title]."</a></td>");
						else printc("<td style='color: #999' class=td$c>created, not yet available</td>");
						
					//check webcourses databases to see if course website was created in course folders (instead of SitesDB)
					} else if ($course_site = coursefoldersite($cl)) {					
						$course_url = urldecode($course_site['url']);
						$title = urldecode($course_site['title']);
						printc("<td style='color: #999' class=td$c><a href='$course_url' target='new_window'>$title</td>");
						db_connect($dbhost, $dbuser, $dbpass, $dbdb);
										
					} else
						printc("<td style='color: #999' class=td$c>not created</td>");
						printc("</tr>");
						$c = 1-$c;
						db_connect($dbhost, $dbuser, $dbpass, $dbdb);
				}
				printc("<tr><td></td><td align=right>Show All Semesters</td></tr>");
				printc("</tr></table>");
	
			}
			printc("</td>");
			printc("</tr>");
			printc("</table>");
	
		}
	}
	// handle group adding backend here
	if (count($group) && ($newgroup || $groupname)) { // they chose a group
		if (!$newgroup) $newgroup = $groupname;
		if (ereg("^[a-zA-Z0-9_-]{1,20}$",$newgroup)) {
			if (db_line_exists("classgroups","name='$newgroup'")) { // already exists
				if (db_line_exists("classgroups","name='$newgroup' and owner='$auser'")) {
					$list = db_get_value("classgroups","classes","name='$newgroup' and owner='$auser'");
					$list = explode(",",$list);
					$newlist = array_unique(array_merge($list,$group));
					$list = implode(",",$newlist);
					$query = "update classgroups set classes='$list' where name='$newgroup' and owner='$auser'";
					log_entry("classgroups","$newgroup","","","$auser updated $newgroup to be $list");
				} else error("Somebody has already created a class group with that name. Please try another name.");
			} else {	// new group
				$list = implode(",",$group);
				$query = "insert into classgroups set name='$newgroup',classes='$list',owner='$auser'";
				log_entry("classgroups","$newgroup","","","$auser added $newgroup with $list");
			}
			db_query($query);
		} else
			error("Your group name is invalid. It may only contain alphanumeric characters, '_', '-', and be under 21 characters. No spaces, punctuation, etc.");

	}

	printc("<div class='title'>Sites".helplink("sites")."</div>");
	
	printc("<form name=groupform action='$PHP_SELF?$sid&action=default' method=post>");
	
	printc("<table width=100%>");
/* 	printc("<tr>"); */
/* 		printc("<th>name</th><th>title</th><th>theme</th><th>status</th><th>active</th><th colspan=2>options</th>"); */
/* 	printc("</tr>"); */
	
	if ($allowpersonalsites) {
		// print out the personal site
		printc("<tr><td class='inlineth' colspan=7>Personal Site</td></tr>");
		printSiteLine($auser);
	}
	
	if ($allowclasssites) {	
		//class sites for professors (for student see above)
		if ($atype == 'prof' && count($classes)) {
			//current classes
			printc("<tr><td class='inlineth' colspan=7>Current Class Sites</td></tr>");
			$gs = array();
			foreach ($classes as $c=>$a) {
				if ($g = inclassgroup($c)) {
					if (!$gs[$g]) printSiteLine($g,$atype);
					$gs[$g] = 1;
				} else
					printSiteLine($c,0,1,$atype);
			}
			//upcoming classes
			if (count($futureclasses)) {		
				printc("<tr><td class='inlineth' colspan=7>Upcoming Classes</td></tr>");
				$gs = array();
				foreach ($futureclasses as $c=>$a) {
					if ($g = inclassgroup($c)) {
						if (!$gs[$g]) printSiteLine($g);
						$gs[$g] = 1;
					} else
						printSiteLine($c,0,1);
				}
			} 	
			
			//info/interface for groups
			printc("<tr><th colspan=7 align=right>add checked sites to group: <input type=text name=newgroup size=10 class=textfield>");
			$r = db_query("select * from classgroups where owner='$auser'");
			$havegroups = db_num_rows($r);
			if ($havegroups) {
				printc(" <select name='groupname' onChange='document.groupform.newgroup.value = document.groupform.groupname.value'>");
				printc("<option value=''>-choose-");
				while ($g = db_fetch_assoc($r)) {
					printc("<option value='$g[name]'>$g[name]\n");
				}
				printc("</select>");
			}
			printc(" <input type=submit class=button value='add'>");
			printc("</th></tr>");
			printc("<tr><th colspan=7 align=left>");
			printc("<div style='padding-left: 10px; font-size: 10px;'>By adding sites to a group you can consolidate multiple class sites into one entity. This is useful if you teach multiple sections of the same class and want to work on only one site for those classes/sections. Check the boxes next to the classes you would like to add, and either type in a new group name or choose an existing one.");
			if ($havegroups) printc("<div class=desc><a href='edit_groups.php?$sid' target='groupeditor' onClick='doWindow(\"groupeditor\",400,400)'>[edit class groups]</a></div>");
			printc("</th></tr>");
				
		}
	}
	
	// get a list of sites for which the user is an editor
/* 	$l = array(); */
/* 	foreach ($classes as $c=>$i) { */
/* 		if ($g = inclassgroup($c)) { */
/* 			$l[]=$g; */
/* 		} else $l[]=$c; */
/* 	} */
//	$query = "select * from sites where editors LIKE '%$auser%'";

	if ($allowclasssites && !$allowpersonalsites)
	  $query = "select * from sites where editors != '' and type!='personal' order by addedtimestamp asc";
	else if (!$allowclasssites && $allowpersonalsites)
	  $query = "select * from sites where editors != '' and type='personal' order by addedtimestamp asc";
	else
	  $query = "select * from sites where editors != '' order by addedtimestamp asc";
	
	$sites = array();
	$r = db_query($query);
	while ($a = db_fetch_assoc($r)) {
		if (is_editor($auser,$a['name'],1)) {
			array_push($sites,$a['name']);
		}
	}
	
	// if they are editors for any sites, they will be in the $sites[] array
	
	if (count($sites)) {
		printc("<tr><td class='inlineth' colspan=7>Sites to which you have editor permissions</td></tr>");
		foreach ($sites as $s) {
			printSiteLine($s,1);
		}
	}
	
	$sites=array();
	if ($allowclasssites && !$allowpersonalsites)
	  $query = "select * from sites where addedby='$auser' and type!='personal' order by addedtimestamp asc";
	else if (!$allowclasssites && $allowpersonalsites)
	  $query = "select * from sites where addedby='$auser' and type='personal' order by addedtimestamp asc";
	else
	  $query = "select * from sites where addedby='$auser' order by addedtimestamp asc";
	$r = db_query($query);
	while ($a=db_fetch_assoc($r)) {
		//printSiteLine($a[name]);
		if (!in_array($a[name],$sitesprinted)) $sites[]=$a[name];
	}
	
	if (count($sites)) {
		printc("<tr><td class='inlineth' colspan=7>Other/Old Sites".helplink("othersites","What are these?")."</td></tr>");
		foreach ($sites as $s)
			printSiteLine($s);
	}
	
	if ($ltype=='admin') printc("<tr><td class='inlineth' colspan=7 align=right><a href='$PHP_SELF?$sid&action=add_site'>add new site</a></td></tr>");
	
	
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
	
	
}

function printSiteLine($name,$ed=0,$isclass=0,$atype='stud') {
	global $color,$possible_themes,$auser;
	global $sitesprinted;
	global $_full_uri;

	if (in_array($name,$sitesprinted)) return;
	$sitesprinted[]=$name;
	
	$isgroup = ($classlist = isgroup($name))?1:0;
	$exists = db_num_rows(db_query("select * from sites where name='$name'"));
	$namelink = ($exists)?"$PHP_SELF?$sid&action=site&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
	$namelink2 = ($exists)?"$PHP_SELF?$sid&action=viewsite&site=$name":"$PHP_SELF?$sid&action=add_site&sitename=$name";
	if ($exists) $a = db_get_line("sites","name='$name'");
	
	printc("<tr>");
	printc("<td class=td$color>");
	$status = ($exists)?"Created":"Not Created";
	if ($exists) {
		if (canview($a)) $active = "<span class=green>active</span>";
		else $active = "<span class=red>(inactive)</span>";
	}
	printc("<table width=100% cellpadding=0 cellspacing=0><tr><td align=left>".(($isclass)?"<input type=checkbox name='group[]' value='$name'> ":"")."$name - ");
	//printc("<td align=right style='font-size: 11px; color: #777;'>");
	if ($exists) {
		printc("<span style ='font-size:14px;'><a href='$namelink'>".$a[title]."</a></span>");
	} else {
		if ($atype == 'prof') {
			printc("<span style ='font-size:10px;'>Create: <a href='$namelink'>Site</a> | <a href='http://etdev.middlebury.edu/mots/prof_add_class?$sid&class=$name' target=new_window>Assessments</a> </span>");
		} else {
			printc("<span style ='font-size:10px;'><a href='$namelink'>Create Site</a></span>");		
		}
	}
	printc("</td><td align=right>");
	printc((($active)?"[$active]":""));
	printc("</td></tr></table>");
	//printc("<div style='padding-left: 20px;'>");
	
	
	if ($isgroup) {
		$list = implode(", ",$classlist);
 		printc("<div style='padding-left: 20px; font-size: 10px;'>  this is a group and contains the following classes: <b>$list</b><br></div>");
	}
	if ($exists) {
		$addedby = $a[addedby];
		$viewpermissions=$a[viewpermissions];
		$added = datetime2usdate($a[addedtimestamp]);
		$edited = $a[editedtimestamp];
		$editedby = $a[editedby];
		printc("<div style='padding-left: 20px; font-size: 10px;'>  added by $addedby on $added".(($editedby)?", edited on ".timestamp2usdate($edited):"")."<br></div>");
		
		if ($a[activatedate] != '0000-00-00' || $a[deactivatedate] != '0000-00-00' || $viewpermissions != 'anyone') {
			printc("<div style='padding-left: 20px; font-size: 10px;'>available: ");
			printc(txtdaterange($a[activatedate],$a[deactivatedate]));
			if ($viewpermissions != 'anyone') {
				printc(" to ");
				if ($viewpermissions == 'midd') printc("Middlebury users");
				if ($viewpermissions == 'class') printc("students in this class");
				
			}
			printc("</div>");
		}

		printc("<div align=left>");
//		printc("<a href='siteeditor.php?$sid&site=$name' target='sitemap' onclick='doWindow(\"sitemap\",600,500)'>[site map]</a><br>");
	//	printc("<a href='$namelink2'>[edit content]</a><br>");
	
/* 		$addr = $_SERVER[SCRIPT_URI]; */
/* 		$l = explode("/",$addr); */
/* 		array_pop($l); */
/* 		$addr = implode("/",$l); */
		$addr = "$_full_uri/sites/$name";
		printc("<div style='padding-left: 20px; font-size: 12px;'>  URL: <a href='$addr' target='_blank'>$addr</a><br></div></div>");
		
		printc("<div align=right>");
				
		if (!$ed) {
			printc(" <a href='$PHP_SELF?$sid&action=viewsite&site=$name'>edit</a> | ");
			printc(" <a href='$PHP_SELF?$sid&action=delete_site&name=$name'>delete</a> | ");
			printc(" <a href='$PHP_SELF?$sid&action=edit_site&edit_site=$name'>settings</a> | ");
			printc(" <a href='editor_access.php?$sid&site=$name' onClick='doWindow(\"permissions\",600,400)' target='permissions'>permissions</a>");
			
		} else {
			printc(" <a href='editor_access.php?$sid&site=$name' onClick='doWindow(\"permissions\",400,400)' target='permissions'>your permissions</a>");
		}
		printc("</div>");
		
		
	}
	
	
	printc("</div>");
	
	printc("</td></tr>");
	
	$color=1-$color;
}

$sitefooter .= "<div align=right style='color: #999; font-size: 10px;'>by <a style='font-weight: normal; text-decoration: underline' href='mailto: gabe@schine.net'>Gabriel Schine</a> and <a href='mailto:achapin@middlebury.edu' style='font-weight: normal; text-decoration: underline'>Alex Chapin</a></div>";
