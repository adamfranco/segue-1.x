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

// gets $user (person trying to edit), $type (SITE,SECTION, or PAGE), and $function (ADD,EDIT, or DELETE)
function permission($user,$type,$function,$id) {
	$user = strtolower($user);
	global $site_owner,$site,$section,$page,$story,$classes;
	// the above is taken as a fix.. i was stupid enough to forget that the addedby field can contain
	// people other than the site owner, so we have to check if the site owner is the current user
	if ($site_owner == $user) return 1;
	if ($type == SITE) { 
		$a = db_get_line("sites","name='$id'");
	}
	if ($type == SECTION) {
		$a = db_get_line("sections","id=$id");
	}
	if ($type == PAGE) {
		$a = db_get_line("pages","id=$id");
		$sectiona = db_get_line("sections","id=$section");
		if ($sectiona[locked]) return 0;
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