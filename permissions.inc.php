<? /* $Id$ */
	// holds permissions functions and checks for people's permissions in accessing certain pages

define("SITE",0);
define("SECTION",1);
define("PAGE",2);
define("STORY",3);

define("ADD",0);
define("EDIT",1);
define("DELETE",2);
define("VIEW",3);
define("DISCUSS",4);

/******************************************************************************
 * PERMISSIONS OBJECT
 ******************************************************************************/

class permissions {

	// defines
	function ADD() {return 0;}
	function EDIT() {return 1;}
	function DELETE() {return 2;}
	function VIEW() {return 3;}
	function DISCUSS() {return 4;}
	
	// for this function to work, the form within which this is called MUST
	// be named 'addform'
	function outputForm(&$o,$d=0) {
		global $cfg;
		$sitename = $o->owning_site;
		if ($_SESSION[settings][edit] && !$o->builtPermissions) $o->buildPermissionsArray();
	
		// ---- Editor actions ----
		if ($_REQUEST[edaction] == 'add') {
			$o->addEditor($_REQUEST[edname]);
		}
		
		if ($_REQUEST[edaction] == 'del') {
			$o->delEditor($_REQUEST[edname]);
		}
		
		printc("<input type=hidden name=edaction value=''>");
		printc("<input type=hidden name=edname value=''>");
		
		if (isclass($sitename)) {
			print "<script lang='javascript'>";
			print "function addClassEditor() {";
			print "	f = document.addform;";
			print "	f.edaction.value='add';";
			print "	f.edname.value='$sitename';";
			print "	f.submit();";
			print "}";
			print "</script>";
		}
		
		$a = array(0=>4,1=>1);
		
		printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
		printc("<table width=100% style='border: 1px solid gray'>");
		printc("<tr><th width=50%>name</th>	<th colspan=".($a[$d])." width=30%>permissions</th><th>del</th></tr>");
		printc("<tr><th>&nbsp;</th>".(($d)?"<th>discuss</th>":"<th>add</th><th>edit</th><th>delete</th><th>view</th>")."<th>&nbsp;</th></tr>");
		if (($edlist = $o->getEditors())) {
			$permissions = $o->getPermissions();
			if (count($edlist)) {
				$color = 0;
				foreach ($edlist as $e) {
					printc("<tr><td class=td$color align=left>");
					if ($e == "everyone")
						printc("Everyone (will override other entries)</td>");
					else if ($e == "institute")
						printc($cfg[inst_name]." Users</td>");
					else
						printc(ldapfname($e)." ($e)</td>");
					
					for ($i = 0; $i<5; $i++) {
						$skip = 0;$nob=0;
						if ($d && $i<4) $skip = 1;
						if (!$d && $i==4) $skip = 1;
						if (!$d && (($e == 'everyone' || $e == 'institute') && $i!=3)) $nob=1;
						if (!$skip) {
							printc("<td class=td$color align=center>");
							if ($nob) printc("&nbsp;");
							else printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($permissions[$e][$i])?" checked":"").">");
							printc("</td>");
						}
						if ($skip || $nob) {
							printc("<input type=hidden name='permissions[$e][$i]' value=".$permissions[$e][$i].">");
						}
					}
					
					printc("</td>");
					printc("<td class=td$color align=center>");
					if ($e == 'everyone' || $e == 'institute') printc("&nbsp;");
					else printc("<a href='#' onClick='delEditor(\"$e\");'>remove</a>");
					printc("</td></tr>");
					$color = 1-$color;
				}
				
			}
		} else printc("<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=".($a[$d]+1).">no editors added</td></tr>");
		printc("<tr><th colspan=".($a[$d]+1).">".((isclass($sitename))?"<a href='#' onClick='addClassEditor();'>Add students in ".$sitename."</a>":"&nbsp;")."</th><th><a href='add_editor.php?$sid' target='addeditor' onClick='doWindow(\"addeditor\",400,250);'>add editor</a></th></tr>");
		printc("</table>");
		
		if ($_SESSION[settings][edit]) printc("<a href='editor_access.php?$sid&site=".$sitename."' onClick='doWindow(\"permissions\",600,400)' target='permissions'>Permissions as of last save</a>");
		
	}
	
}

/* ***************************************************************************** */
/*  * END OBJECT */
/*  ***************************************************************************** */
/*  */
/*  */
/* function has_permissions($user, $this_level,$site,$section,$page,$story) { */
/* 	// gets $user (person trying to edit), $type (SITE, SECTION, PAGE, or STORY) */
/* 	// if $section, $page, or $story is undefined, use "" instead of a value. */
/* 	global $site_owner; */
/* 	 */
/* //	print "<p>\$user = $user <br>\$this_level = $this_level <br>\$site = $site <br>\$section = $section <br>\$page = $page <br>\$story = $story<br>"; */
/*  */
/* 	if ($site_owner == $user) return 1; */
/*  */
/* 	$site_permissions = 0; */
/* 	$section_permissions = 0; */
/* 	$page_permissions = 0; */
/* 	$story_permissions = 0; */
/* 	 */
/* 	if ($this_level == SITE) {	 */
/* 		$a = permission($user, SITE, ADD, $site); */
/* 		$e = permission($user, SITE, EDIT, $site); */
/* 		$d = permission($user, SITE, DELETE, $site); */
/* 		if ($a || $e || $d) $site_permissions = 1; */
/* 		 */
/* 		$sections = decode_array(db_get_value("sites","sections","name='$site'")); */
/* 		foreach ($sections as $section) { */
/* 			$a = permission($user, SECTION, ADD, $section); */
/* 			$e = permission($user, SECTION, EDIT, $section); */
/* 			$d = permission($user, SECTION, DELETE, $section); */
/* 			if ($a || $e || $d) $section_permissions = 1; */
/* 				 */
/* 			$pages = decode_array(db_get_value("sections","pages","id=$section")); */
/* 			foreach ($pages as $page) { */
/* 				$a = permission($user, PAGE, ADD, $page); */
/* 				$e = permission($user, PAGE, EDIT, $page); */
/* 				$d = permission($user, PAGE, DELETE, $page); */
/* 				if ($a || $e || $d) $page_permissions = 1; */
/* 	 */
/* 				$stories = decode_array(db_get_value("pages","stories","id=$page")); */
/* 				foreach ($stories as $story) { */
/* 					$a = permission($user, STORY, ADD, $story); */
/* 					$e = permission($user, STORY, EDIT, $story); */
/* 					$d = permission($user, STORY, DELETE, $story); */
/* 					if ($a || $e || $d) $story_permissions = 1; */
/* 				} */
/* 			} */
/* 		} */
/* 	} */
/*  */
/* 	if ($this_level == SECTION) { */
/* 		$a = permission($user, SITE, ADD, $site); */
/* 		$e = permission($user, SITE, EDIT, $site); */
/* 		$d = permission($user, SITE, DELETE, $site); */
/* 		if ($a || $e || $d) $site_permissions = 1; */
/* 		 */
/* 		$a = permission($user, SECTION, ADD, $section); */
/* 		$e = permission($user, SECTION, EDIT, $section); */
/* 		$d = permission($user, SECTION, DELETE, $section); */
/* 		if ($a || $e || $d) $section_permissions = 1; */
/* 				 */
/* 		$pages = decode_array(db_get_value("sections","pages","id=$section")); */
/* 		foreach ($pages as $page) { */
/* 			$a = permission($user, PAGE, ADD, $page); */
/* 			$e = permission($user, PAGE, EDIT, $page); */
/* 			$d = permission($user, PAGE, DELETE, $page); */
/* 			if ($a || $e || $d) $page_permissions = 1; */
/* 	 */
/* 			$stories = decode_array(db_get_value("pages","stories","id=$page")); */
/* 			foreach ($stories as $story) { */
/* 				$a = permission($user, STORY, ADD, $story); */
/* 				$e = permission($user, STORY, EDIT, $story); */
/* 				$d = permission($user, STORY, DELETE, $story); */
/* 				if ($a || $e || $d) $story_permissions = 1; */
/* 			} */
/* 		} */
/* 	} */
/* 	 */
/* 	if ($this_level == PAGE) { */
/* 		$a = permission($user, SITE, ADD, $site); */
/* 		$e = permission($user, SITE, EDIT, $site); */
/* 		$d = permission($user, SITE, DELETE, $site); */
/* 		if ($a || $e || $d) $site_permissions = 1; */
/* 		 */
/* 		$a = permission($user, SECTION, ADD, $section); */
/* 		$e = permission($user, SECTION, EDIT, $section); */
/* 		$d = permission($user, SECTION, DELETE, $section); */
/* 		if ($a || $e || $d) $section_permissions = 1; */
/* 				 */
/* 		$a = permission($user, PAGE, ADD, $page); */
/* 		$e = permission($user, PAGE, EDIT, $page); */
/* 		$d = permission($user, PAGE, DELETE, $page); */
/* 		if ($a || $e || $d) $page_permissions = 1; */
/* 	 */
/* 		$stories = decode_array(db_get_value("pages","stories","id=$page")); */
/* 		foreach ($stories as $story) { */
/* 			$a = permission($user, STORY, ADD, $story); */
/* 			$e = permission($user, STORY, EDIT, $story); */
/* 			$d = permission($user, STORY, DELETE, $story); */
/* 			if ($a || $e || $d) $story_permissions = 1; */
/* 		} */
/* 	} */
/*  */
/* 	if ($this_level == STORY) { */
/* 		$a = permission($user, SITE, ADD, $site); */
/* 		$e = permission($user, SITE, EDIT, $site); */
/* 		$d = permission($user, SITE, DELETE, $site); */
/* 		if ($a || $e || $d) $site_permissions = 1; */
/* 		 */
/* 		$a = permission($user, SECTION, ADD, $section); */
/* 		$e = permission($user, SECTION, EDIT, $section); */
/* 		$d = permission($user, SECTION, DELETE, $section); */
/* 		if ($a || $e || $d) $section_permissions = 1; */
/* 				 */
/* 		$a = permission($user, PAGE, ADD, $page); */
/* 		$e = permission($user, PAGE, EDIT, $page); */
/* 		$d = permission($user, PAGE, DELETE, $page); */
/* 		if ($a || $e || $d) $page_permissions = 1; */
/* 	 */
/* 		$a = permission($user, STORY, ADD, $story); */
/* 		$e = permission($user, STORY, EDIT, $story); */
/* 		$d = permission($user, STORY, DELETE, $story); */
/* 		if ($a || $e || $d) $story_permissions = 1; */
/* 	} */
/* 	 */
/* //	print "\$site_permissions = $site_permissions <br>\$section_permissions = $section_permissions <br>\$page_permissions = $page_permissions <br>\$story_permissions = $story_permissions</p><br>"; */
/* 	 */
/* 	if ($site_permissions || $section_permissions || $page_permissions || $story_permissions ) return 1; */
/* 	else return 0; */
/* } */
/*  */
/* // gets $user (person trying to edit), $type (SITE,SECTION, or PAGE), and $function (ADD,EDIT, or DELETE) */
/* function permission($user,$type,$function,$id) { */
/* 	$user = strtolower($user); */
/* 	global $classes; */
/* 	 */
/* 	$debug = 0; */
/* 	// debug */
/* 	if ($debug) print("$user - $type - $function - $id<BR><BR>"); */
/*  */
/* 	if ($type == SITE) {  */
/* 		$a = db_get_line("sites","name='$id'"); */
/* 		$site_owner = db_get_value("sites","addedby","name='$id'"); */
/* 		$site = $id; */
/* 		if ($site_owner == $user) {if ($debug) print "return 1"; return 1;} */
/* 	} */
/* 	if ($type == SECTION) { */
/* 		$a = db_get_line("sections","id=$id"); */
/* 		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'"); */
/* 		$site = $a[site_id]; */
/* 		if ($site_owner == $user) {if ($debug) print "return 1"; return 1;} */
/* 	} */
/* 	if ($type == PAGE) { */
/* 		$a = db_get_line("pages","id=$id"); */
/* 		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'"); */
/* 		$site = $a[site_id]; */
/* 		$section = $a[section_id]; */
/* 		if ($site_owner == $user) {if ($debug) print "return 1"; return 1;} */
/* 		$sectiona = db_get_line("sections","id=$section"); */
/* 		if ($sectiona[locked]) {if ($debug) print "return 0"; return 0;} */
/* 	} */
/* 	if ($type == STORY) { */
/* 		$a = db_get_line("stories","id=$id"); */
/* 		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'"); */
/* 		$site = $a[site_id]; */
/* 		$section = $a[section_id]; */
/* 		$page = $a[page_id]; */
/* 		if ($site_owner == $user) {if ($debug) print "return 1"; return 1;} */
/* 		$sectiona = db_get_line("sections","id=$section"); */
/* 		if ($sectiona[locked]) {if ($debug) print "return 0"; return 0;} */
/* 		$pagea = db_get_line("pages","id=$page"); */
/* 		if ($pagea[locked]) {if ($debug) print "return 0"; return 0;} */
/* 	} */
/*  */
/* 	 */
/* 	if ($a['locked'] && $user != $site_owner) {if ($debug) print "return 0"; return 0;} */
/* 	 */
/* 	$permissions = decode_array($a['permissions']); */
/*  */
/* //	print "<pre>";print_r($permissions);print"</pre>"; //debug */
/* 	 */
/* 	// check class permissions -- are they in the class? */
/* 	if (isclass($site)) { */
/* //		print "is class"; //debug */
/* 		foreach ($permissions as $e=>$p) { */
/* 			if (isclass($e)) { */
/* 				$l = array(); */
/* 				if ($r = isgroup($e)) { */
/* 					$l = $r; */
/* 				} else $l[]=$e; */
/* 				foreach ($l as $c) { */
/* 					if ($classes[$c]) $user = $e; */
/* 				} */
/* 			} */
/* 		} */
/* 	} */
/* 	if ($debug) print "return ".$permissions[$user][$function]; */
/* 	return $permissions[$user][$function]; */
/*  */
/* } */
/*  */
/* function is_editor($user,$site,$ignore_owner=0) { */
/* 	$user = strtolower($user);	 */
/* 	$classes = getuserclasses($user); */
/* //	print "<pre>";print_r($classes);print "</pre>"; */
/* 	$a = db_get_line("sites","name='$site'"); */
/* 	$owner = $a[addedby]; */
/* 	$editors = explode(",",$a[editors]); */
/* 	if (!$ignore_owner) */
/* 		if ($user == $owner) return 1; */
/* 	if (in_array($user,$editors)) return 1; */
/* 	if (in_array(strtolower($user),$editors)) return 1; */
/* //	print "<pre>editors: ";print_r($editors);print "</pre>"; //debug */
/* 	foreach ($editors as $e) { */
/* 		if (isclass($e)) { */
/* 			$l=array(); */
/* 			if ($r = isgroup($e)) { */
/* //				print "is group"; */
/* 				$l = $r; */
/* 			} else $l[]=$e; */
/* //			print "<pre>";print_r($l);print "</pre>"; //debug */
/* //			print "<pre>";print_r($classes[$l[0]]);print "</pre>"; //debug */
/* 			foreach ($l as $c) { */
/* 				if ($classes[$c]) return 1; */
/* 			} */
/* 		} else if (strtolower($user) == strtolower($e)) return 1; */
/* 		print "$user - $e<BR>"; */
/* 	} */
/* 	return 0; */
/* } */
/*  */
/* // $pe = print errors or not (1 or 0) */
/* function siteviewpermissions($siteinfo,$pe=0) { */
/* 	global $auser,$_loggedin,$lmethod,$amethod,$error,$REMOTE_ADDR,$classes; */
/* 	$x=0; */
/* 	if ($siteinfo[addedby] == $auser) return 1; */
/* 	if (!$siteinfo[active]) { */
/* 		error("This site has not yet been activated by the site owner. Please contact them if you believe this is in error or come back at a later time.",$pe); */
/* 		$x=1; */
/* 	} */
/* 	if (!indaterange($siteinfo[activatedate],$siteinfo[deactivatedate])) { */
/* 		error("This site is not yet available. Come back between <b>".txtdaterange($siteinfo[activatedate],$siteinfo[deactivatedate])."</b> to view this site.",$pe); */
/* 		$x=1; */
/* 	} */
/* 	 */
/* 	// first check if we're allowed to view this site */
/* 	if ($siteinfo[viewpermissions] == 'midd') { */
/* 		if (!ereg("140.233.([0-9]{1,3}).([0-9]{1,3})",$REMOTE_ADDR)) { */
/* 			if (!($_loggedin && $lmethod=='ldap')) { */
/* 				error("In order to view this site, you must either be on the $cfg[inst_name] network, or logged in with your username and password.",$pe); */
/* 				$x=1; */
/* 			} */
/* 		} */
/* 	} */
/* 	if ($siteinfo[viewpermissions] == 'class') { */
/* 		$isgood=0; */
/* 		$e = $siteinfo[name]; */
/* 		if (isclass($e)) { */
/* 			$l=array(); */
/* 			if ($r = isgroup($e)) { */
/* 				$l = $r; */
/* 			} else $l[]=$e; */
/* 			foreach ($l as $c) { */
/* 				if ($classes[$c]) $isgood = 1; */
/* 			} */
/* 		} */
/* 		 */
/* 		if (!$isgood) { */
/* 			error("You must be a student in this class in order to view the site. Log in above with your Middlebury username and password to view the site.",$pe); */
/* 			$x=1; */
/* 		} */
/* 	} */
/* 	return !$x; */
/* } */
/*  */
/* function candiscuss($a,$pe=0) { */
/* 	global $auser, $_loggedin,$lmethod,$amethod,$error,$REMOTE_ADDR,$site,$classes; */
/* 	if (!$classes) $classes=getuserclasses($auser); */
/* 	$x=0; */
/* 	if ($a[discusspermissions]=='midd') { */
/* 		if (!$_loggedin && $lmethod!='ldap') { */
/* 			error("In order to discuss this story, you must be a $cfg[inst_name] user. Please log in above to begin dicussion.",$pe); */
/* 			$x=1; */
/* 		} */
/* 	} */
/* 	if ($a[discusspermissions]=='class') { */
/* 		$isgood=0; */
/* 		// check if they're in the class */
/* 		$e=$site; */
/* 		if (isclass($e)) { */
/* 			$l=array(); */
/* 			if ($r = isgroup($e)) { */
/* 				$l = $r; */
/* 			} else $l[]=$e; */
/* //			print_r($l); */
/* 			foreach ($l as $c) { */
/* 				if ($classes[$c]) $isgood = 1; */
/* 			} */
/* //			print_r($isgood); */
/* //			print_r($classes); */
/* 		} */
/* 		if (!$isgood) { */
/* 			error("You must be a student in this class in order to discuss this story. Please log in above.",$pe); */
/* 			$x=1; */
/* 		} */
/* 	} */
/* 	return !$x; */
/* } */