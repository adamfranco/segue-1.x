<? // permissions.inc.php
	// holds permissions functions and checks for people's permissions in accessing certain pages

define("SITE",0);
define("SECTION",1);
define("PAGE",2);
define("STORY",3);

define("ADD",0);
define("EDIT",1);
define("DELETE",2);

// code..... to be written
if ($site) $site_owner = db_get_value("sites","addedby","name='$site'");

function has_permissions($user, $this_level,$site,$section,$page,$story) {
	// gets $user (person trying to edit), $type (SITE, SECTION, PAGE, or STORY)
	// if $section, $page, or $story is undefined, use "" instead of a value.
	global $site_owner;
	
//	print "<p>\$user = $user <br>\$this_level = $this_level <br>\$site = $site <br>\$section = $section <br>\$page = $page <br>\$story = $story<br>";

	if ($site_owner == $user) return 1;

	$site_permissions = 0;
	$section_permissions = 0;
	$page_permissions = 0;
	$story_permissions = 0;
	
	if ($this_level == SITE) {	
		$a = permission($user, SITE, ADD, $site);
		$e = permission($user, SITE, EDIT, $site);
		$d = permission($user, SITE, DELETE, $site);
		if ($a || $e || $d) $site_permissions = 1;
		
		$sections = decode_array(db_get_value("sites","sections","name='$site'"));
		foreach ($sections as $section) {
			$a = permission($user, SECTION, ADD, $section);
			$e = permission($user, SECTION, EDIT, $section);
			$d = permission($user, SECTION, DELETE, $section);
			if ($a || $e || $d) $section_permissions = 1;
				
			$pages = decode_array(db_get_value("sections","pages","id=$section"));
			foreach ($pages as $page) {
				$a = permission($user, PAGE, ADD, $page);
				$e = permission($user, PAGE, EDIT, $page);
				$d = permission($user, PAGE, DELETE, $page);
				if ($a || $e || $d) $page_permissions = 1;
	
				$stories = decode_array(db_get_value("pages","stories","id=$page"));
				foreach ($stories as $story) {
					$a = permission($user, STORY, ADD, $story);
					$e = permission($user, STORY, EDIT, $story);
					$d = permission($user, STORY, DELETE, $story);
					if ($a || $e || $d) $story_permissions = 1;
				}
			}
		}
	}

	if ($this_level == SECTION) {
		$a = permission($user, SITE, ADD, $site);
		$e = permission($user, SITE, EDIT, $site);
		$d = permission($user, SITE, DELETE, $site);
		if ($a || $e || $d) $site_permissions = 1;
		
		$a = permission($user, SECTION, ADD, $section);
		$e = permission($user, SECTION, EDIT, $section);
		$d = permission($user, SECTION, DELETE, $section);
		if ($a || $e || $d) $section_permissions = 1;
				
		$pages = decode_array(db_get_value("sections","pages","id=$section"));
		foreach ($pages as $page) {
			$a = permission($user, PAGE, ADD, $page);
			$e = permission($user, PAGE, EDIT, $page);
			$d = permission($user, PAGE, DELETE, $page);
			if ($a || $e || $d) $page_permissions = 1;
	
			$stories = decode_array(db_get_value("pages","stories","id=$page"));
			foreach ($stories as $story) {
				$a = permission($user, STORY, ADD, $story);
				$e = permission($user, STORY, EDIT, $story);
				$d = permission($user, STORY, DELETE, $story);
				if ($a || $e || $d) $story_permissions = 1;
			}
		}
	}
	
	if ($this_level == PAGE) {
		$a = permission($user, SITE, ADD, $site);
		$e = permission($user, SITE, EDIT, $site);
		$d = permission($user, SITE, DELETE, $site);
		if ($a || $e || $d) $site_permissions = 1;
		
		$a = permission($user, SECTION, ADD, $section);
		$e = permission($user, SECTION, EDIT, $section);
		$d = permission($user, SECTION, DELETE, $section);
		if ($a || $e || $d) $section_permissions = 1;
				
		$a = permission($user, PAGE, ADD, $page);
		$e = permission($user, PAGE, EDIT, $page);
		$d = permission($user, PAGE, DELETE, $page);
		if ($a || $e || $d) $page_permissions = 1;
	
		$stories = decode_array(db_get_value("pages","stories","id=$page"));
		foreach ($stories as $story) {
			$a = permission($user, STORY, ADD, $story);
			$e = permission($user, STORY, EDIT, $story);
			$d = permission($user, STORY, DELETE, $story);
			if ($a || $e || $d) $story_permissions = 1;
		}
	}

	if ($this_level == STORY) {
		$a = permission($user, SITE, ADD, $site);
		$e = permission($user, SITE, EDIT, $site);
		$d = permission($user, SITE, DELETE, $site);
		if ($a || $e || $d) $site_permissions = 1;
		
		$a = permission($user, SECTION, ADD, $section);
		$e = permission($user, SECTION, EDIT, $section);
		$d = permission($user, SECTION, DELETE, $section);
		if ($a || $e || $d) $section_permissions = 1;
				
		$a = permission($user, PAGE, ADD, $page);
		$e = permission($user, PAGE, EDIT, $page);
		$d = permission($user, PAGE, DELETE, $page);
		if ($a || $e || $d) $page_permissions = 1;
	
		$a = permission($user, STORY, ADD, $story);
		$e = permission($user, STORY, EDIT, $story);
		$d = permission($user, STORY, DELETE, $story);
		if ($a || $e || $d) $story_permissions = 1;
	}
	
//	print "\$site_permissions = $site_permissions <br>\$section_permissions = $section_permissions <br>\$page_permissions = $page_permissions <br>\$story_permissions = $story_permissions</p><br>";
	
	if ($site_permissions || $section_permissions || $page_permissions || $story_permissions ) return 1;
	else return 0;
}

// gets $user (person trying to edit), $type (SITE,SECTION, or PAGE), and $function (ADD,EDIT, or DELETE)
function permission($user,$type,$function,$id) {
	$user = strtolower($user);
	global $classes;

	if ($type == SITE) { 
		$a = db_get_line("sites","name='$id'");
		$site_owner = db_get_value("sites","addedby","name='$id'");
		$site = $id;
		if ($site_owner == $user) return 1;
	}
	if ($type == SECTION) {
		$a = db_get_line("sections","id=$id");
		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'");
		$site = $a[site_id];
		if ($site_owner == $user) return 1;
	}
	if ($type == PAGE) {
		$a = db_get_line("pages","id=$id");
		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'");
		$site = $a[site_id];
		$section = $a[section_id];
		if ($site_owner == $user) return 1;
		$sectiona = db_get_line("sections","id=$section");
		if ($sectiona[locked]) return 0;
	}
	if ($type == STORY) {
		$a = db_get_line("stories","id=$id");
		$site_owner = db_get_value("sites","addedby","name='$a[site_id]'");
		$site = $a[site_id];
		$section = $a[section_id];
		$page = $a[page_id];
		if ($site_owner == $user) return 1;
		$sectiona = db_get_line("sections","id=$section");
		if ($sectiona[locked]) return 0;
		$pagea = db_get_line("pages","id=$page");
		if ($pagea[locked]) return 0;
	}

	
	if ($a['locked'] && $user != $site_owner) return 0;
	
	$permissions = decode_array($a['permissions']);

//	print "<pre>";print_r($permissions);print"</pre>"; //debug
	
	// check class permissions -- are they in the class?
	if (isclass($site)) {
//		print "is class"; //debug
		foreach ($permissions as $e=>$p) {
			if (isclass($e)) {
				$l = array();
				if ($r = isgroup($e)) {
					$l = $r;
				} else $l[]=$e;
				foreach ($l as $c) {
					if ($classes[$c]) $user = $e;
				}
			}
		}
	}
	return $permissions[$user][$function];

}

function is_editor($user,$site,$ignore_owner=0) {
	$user = strtolower($user);	
	$classes = getuserclasses($user);
//	print "<pre>";print_r($classes);print "</pre>";
	$a = db_get_line("sites","name='$site'");
	$owner = $a[addedby];
	$editors = explode(",",$a[editors]);
	if (!$ignore_owner)
		if ($user == $owner) return 1;
/* 	if (in_array($user,$editors)) return 1; */
/* 	if (in_array(strtolower($user),$editors)) return 1; */
//	print "<pre>editors: ";print_r($editors);print "</pre>"; //debug
	foreach ($editors as $e) {
		if (isclass($e)) {
			$l=array();
			if ($r = isgroup($e)) {
//				print "is group";
				$l = $r;
			} else $l[]=$e;
//			print "<pre>";print_r($l);print "</pre>"; //debug
//			print "<pre>";print_r($classes[$l[0]]);print "</pre>"; //debug
			foreach ($l as $c) {
				if ($classes[$c]) return 1;
			}
		} else if (strtolower($user) == strtolower($e)) return 1;
/* 		print "$user - $e<BR>"; */
	}
	return 0;
}

// $pe = print errors or not (1 or 0)
function siteviewpermissions($siteinfo,$pe=0) {
	global $auser,$_loggedin,$lmethod,$amethod,$error,$REMOTE_ADDR,$classes;
	$x=0;
	if ($siteinfo[addedby] == $auser) return 1;
	if (!$siteinfo[active]) {
		error("This site has not yet been activated by the site owner. Please contact them if you believe this is in error or come back at a later time.",$pe);
		$x=1;
	}
	if (!indaterange($siteinfo[activatedate],$siteinfo[deactivatedate])) {
		error("This site is not yet available. Come back between <b>".txtdaterange($siteinfo[activatedate],$siteinfo[deactivatedate])."</b> to view this site.",$pe);
		$x=1;
	}
	
	// first check if we're allowed to view this site
	if ($siteinfo[viewpermissions] == 'midd') {
		if (!ereg("140.233.([0-9]{1,3}).([0-9]{1,3})",$REMOTE_ADDR)) {
			if (!($_loggedin && $lmethod=='ldap')) {
				error("In order to view this site, you must either be on the Middlebury College campus, or logged in with your Middlebury username and password.",$pe);
				$x=1;
			}
		}
	}
	if ($siteinfo[viewpermissions] == 'class') {
		$isgood=0;
		$e = $siteinfo[name];
		if (isclass($e)) {
			$l=array();
			if ($r = isgroup($e)) {
				$l = $r;
			} else $l[]=$e;
			foreach ($l as $c) {
				if ($classes[$c]) $isgood = 1;
			}
		}
		
		if (!$isgood) {
			error("You must be a student in this class in order to view the site. Log in above with your Middlebur username and password to view the site.",$pe);
			$x=1;
		}
	}
	return !$x;
}

function candiscuss($a,$pe=0) {
	global $auser, $_loggedin,$lmethod,$amethod,$error,$REMOTE_ADDR,$site,$classes;
	if (!$classes) $classes=getuserclasses($auser);
	$x=0;
	if ($a[discusspermissions]=='midd') {
		if (!$_loggedin && $lmethod!='ldap') {
			error("In order to discuss this story, you must be a Middlebury user. Please log in above to begin dicussion.",$pe);
			$x=1;
		}
	}
	if ($a[discusspermissions]=='class') {
		$isgood=0;
		// check if they're in the class
		$e=$site;
		if (isclass($e)) {
			$l=array();
			if ($r = isgroup($e)) {
				$l = $r;
			} else $l[]=$e;
//			print_r($l);
			foreach ($l as $c) {
				if ($classes[$c]) $isgood = 1;
			}
//			print_r($isgood);
//			print_r($classes);
		}
		if (!$isgood) {
			error("You must be a student in this class in order to discuss this story. Please log in above.",$pe);
			$x=1;
		}
	}
	return !$x;
}