<?	// index.php for coursesDB module
	// this file controls pretty much the entire program, taking input and executing the correct scripts accordingly

// we need to include object files before session_start() or registered
// objects will be broken.
include("objects/objects.inc.php");

ob_start();		// start the output buffer so we can use headers if needed
session_start();// start the session manager :) -- important, as we just learned

/* if (!ini_get("register_globals")) { */
/* 	if ($HTTP_POST_VARS) { */
/* 		foreach ($HTTP_POST_VARS as $n=>$v) */
/* 			$$n = $v; */
/* 	} */
/* 	if ($HTTP_GET_VARS) { */
/* 		foreach ($HTTP_GET_VARS as $n=>$v) */
/* 			$$n = $v; */
/* 	} */
/* 	if ($HTTP_SESSION_VARS) { */
/* 		foreach ($HTTP_SESSION_VARS as $n=>$v) $$n = $v; */
/* 	} */
/* 	if ($_SESSION) { */
/* 		foreach ($_SESSION as $n=>$v) $$n = $v; */
/* 	} */
/* } */

if (ereg("^login",getenv("QUERY_STRING"))) {
	if (session_id()) {
		session_unset();
		session_destroy();
	}
	header("Location: index.php");
}

// if they clicked a 'goback' button
if ($goback && $gobackurl) {
	header("Location: $gobackurl");
	exit;
}

// initialize the content variables
$leftnav = $rightnav = $topnav = array();
$content= "";
$leftnav_extra = $rightnav_extra = $topnav_extra = '';

if (!$action) { $action="default"; }		// if there's no action, use default
if (ereg("\.",$action)) $action="no_action";	// security to prevent someone from setting an action to add_user.admin or something (maybe unneeded, but can't hurt)

// include all necessary files
include("includes.inc.php");

// if we are logged in, get a list of classes the user has
// but only if login method was LDAP.. otherwise don't waste the time
$classes=array();
$oldclasses=array();
$futureclasses=array();
$oldsites=array();

if ($_loggedin) {

	// below if statement should be changed to check a config variable that states
	// if classes should be check, and what routine to use to get that
	$classes=getuserclasses($auser,"now");
	$oldclasses=getuserclasses($auser,"past");
	$futureclasses=getuserclasses($auser,"future");

	
	// get other sites they have added, but which aren't in the classes list
	if (db_num_rows($r = db_query("select * from sites where addedby='$auser'"))) {
		while ($a = db_fetch_assoc($r)) {
			$n = $a['name'];
			if (!is_array($classes[$n]) && isclass($n)) {
				$oldsites[]=$n;
			}
		}
	}
}

//if (count($classes)) printc(implode(",",array_keys($classes)));


// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// set up theme, header,footer and navlinks
if ($site) {						// we are in a site
	$siteinfo = db_get_line("sites","name='$site'");
	$site_owner = $siteinfo[addedby];
	if ($HTTP_GET_VARS[theme]) $sid .= "&theme=$theme";
	if ($HTTP_GET_VARS[themesettings]) {$themesettings=urlencode(stripslashes($themesettings)); $sid.="&themesettings=$themesettings";}
	if (!isset($theme)) $theme = $siteinfo[theme];
	if (!isset($themesettings)) $themesettings = $siteinfo[themesettings];

	$siteheader = "<div align=center style='margin-bottom: 3px'>";
	$st = stripslashes(urldecode($siteinfo[header]));
	$st = str_replace("src='####","####",$st);
	$st = str_replace("src=####","####",$st);
	$st = str_replace("####'","####",$st);
	$textarray1 = explode("####", $st);
	if (count($textarray1) > 1) {
		for ($i=1; $i<count($textarray1); $i=$i+2) {
			$id = $textarray1[$i];
			$filename = urldecode(db_get_value("media","name","id=$id"));
			$userdir = db_get_value("media","site_id","id=$id");
			$filepath = $uploadurl."/".$userdir."/".$filename;
			$textarray1[$i] = "src='".$filepath."'";
		}		
		$st = implode("",$textarray1);
	}
	$siteheader .= $st;
	$siteheader .= "</div>";

	$sitefooter = "<center>";
	$st = stripslashes(urldecode($siteinfo[footer]));
	$st = str_replace("src='####","####",$st);
	$st = str_replace("src=####","####",$st);
	$st = str_replace("####'","####",$st);
	$textarray1 = explode("####", $st);
	if (count($textarray1) > 1) {
		for ($i=1; $i<count($textarray1); $i=$i+2) {
			$id = $textarray1[$i];
			$filename = urldecode(db_get_value("media","name","id=$id"));
			$userdir = db_get_value("media","site_id","id=$id");
			$filepath = $uploadurl."/".$userdir."/".$filename;
			$textarray1[$i] = "src='".$filepath."'";
		}		
		$st = implode("",$textarray1);
	}
	$sitefooter .= $st;
	$sitefooter .= "</center>";
}


// if we don't already have content (probably login error messages), then output some shite
if (!$loginerror) {
	// debug -- make sure we're logged in :)
/* 	print "You are logged in using $luser" . (($ltype=='admin' && $luser != $auser)?", acting as $auser":"").". Your email is $lemail.<BR><BR>"; */
/* 	if ($ltype == 'admin') { */
/* 		print "<form action='$PHP_SELF?$sid' method=post>"; */
/* 		print "<input type=hidden name=action value='change_auser'>"; */
/* 		print "Change active user: <input type=text name='name' size=15> <input type=submit value=Go>"; */
/* 		print "</form>"; */
/* 	} */
	
	$try = "$action.$ltype.inc.php";			// first try a ltype-specific action file
	if (file_exists($try)) {					// yes, indeed
		include($try);
	} else {
		$try = "$action.inc.php";				// try a general one
		if (file_exists($try)) include($try);
		else include("no_action.inc.php");		// action not implemented yet or doesn't exist :(
	}
}

// output any errors that may exist to the content variables
printerr();

/****************************************************************************/
/*					use content vars from above and print out content		*/

$t = $action;
if ($t != 'site') $t = 'viewsite';
if ($section) {
	$sectioninfo = db_get_line("sections","id=$section");
	$sn = " > <a href='$PHP_SELF?$sid&action=$t&site=$site&section=$section'>$sectioninfo[title]</a>";
}
if ($page) {		// we're viewing a page
	$pageinfo = db_get_line("pages","id=$page");
	$pn = " > <a href='$PHP_SELF?$sid&action=$t&site=$site&section=$section&page=$page'>$pageinfo[title]</a>";
}
if ($site)
	$nav = "<a href='$PHP_SELF?$sid&action=$t&site=$site'>$siteinfo[title]</a>";
	$title = "$siteinfo[title]";
$nav .= $sn.$pn;
if ($nav)
	//$siteheader = $siteheader . "<div align=left style='margin-bottom: 5px; margin-left: 10px'>$nav</div>";
	$sitecrumbs = "<div align=left style='margin-bottom: 5px; margin-left: 10px; font-size: 9px'>$nav</div>";

// Load non-pervasive theme for "program" actions
// the theme and settings are defined in the config.inc.php
if (!$pervasivethemes && ($action == "edit_site" || $action == "add_site" || $action == "add_section" || $action == "edit_section" || $action == "add_page" || $action == "edit_page" || $action == "add_story" || $action == "edit_story")) {
	$theme = $programtheme;
	$themesettings = $programthemesettings;
}

// if there isn't any other theme set, use the default theme
if (!$theme) {
	$theme = $defaulttheme;
	$themesettings = $defaultthemesettings;
}

// get theme default settings

/* if (file_exists("$themesdir/$theme/defaults.inc.php")) */
/* 	include("$themesdir/$theme/defaults.inc.php"); */


//output a meta tag
//print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// decode themesettings
if ($themesettings) $themesettings = decode_array($themesettings);
//print "$themesdir/$theme/";



//output the HTML
include("$themesdir/$theme/output.inc.php");

// ------------------
// if register_globals is off, we have to do some hacking to get things to work:
if (!ini_get("register_globals")) {
	foreach (array_keys($_SESSION) as $n) $_SESSION[$n] = $$n;
}

?>