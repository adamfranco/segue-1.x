<? /* $Id$ */
	// this file controls pretty much the entire program, taking input and executing the correct scripts accordingly

// we need to include object files before session_start() or registered
// objects will be broken.
include("objects/objects.inc.php");

ob_start();		// start the output buffer so we can use headers if needed
session_start();// start the session manager :) -- important, as we just learned

if (ereg("^login",getenv("QUERY_STRING"))) {
	if (session_id()) {
		session_unset();
		session_destroy();
	}
	header("Location: index.php");
}

// actions for which we use pervasive themes (if enabled)
// and actions for which certain session variables are allowed (settings, *Obj)
$pervasiveActions = array("edit_site","add_site","add_section","edit_section","add_page","edit_page","add_story","edit_story");
$permittedSettingsActions = $pervasiveActions;
$permittedSettingsActions[]='site';
$permittedPermissionsActions = array("site","viewsite");

// check if we have any stray session variables
if (!in_array($_REQUEST[action],$permittedSettingsActions)) {
	if ($_SESSION[settings] || $_SESSION[siteObj] || $_SESSION[sectionObj] || $_SESSION[pageObj] || $_SESSION[storyObj]) {
		if (ini_get("register_globals")) {
			session_unregister("settings");
			session_unregister("siteObj");
			session_unregister("sectionObj");
			session_unregister("pageObj");
			session_unregister("storyObj");
		}
		unset($_SESSION[settings],$_SESSION[siteObj],$_SESSION[sectionObj],$_SESSION[pageObj],$_SESSION[storyObj]);
	}
}
/* if (!in_array($_REQUEST[action],$permittedPermissionsActions)) { */
/* 	if (isset($_SESSION[editors]) || isset($_SESSION[obj])) unset($_SESSION[obj],$_SESSION[editors]); */
/* } */

// if they clicked a 'goback' button -- this is OBSOLETE
/* if ($goback && $gobackurl) { */
/* 	header("Location: $gobackurl"); */
/* 	exit; */
/* } */

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

	// one array containing all user's classes
	$allclasses = array_merge($classes,$oldclasses,$futureclasses);
	
	// get other sites they have added, but which aren't in the classes list
	if ($s = segue::getAllSites($_SESSION[auser])) {
		foreach ($s as $n) {
/* 			$n = $a['name']; */
			if (!is_array($allclasses[$n]) && isclass($n)) {
				$oldsites[]=$n;
			}
		}
	}
}


//if (count($classes)) printc(implode(",",array_keys($classes)));

/* --------------- eventually, this command will be gone... unneeded and handled by ADOdb */
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// if we have info stored in settings session var, get some of it
if ($_SESSION[settings][site]) $_REQUEST[site] = $_SESSION[settings][site];
if ($_SESSION[settings][section]) $_REQUEST[section] = $_SESSION[settings][section];
if ($_SESSION[settings][page]) $_REQUEST[page] = $_SESSION[settings][page];
if ($_SESSION[settings][story]) $_REQUEST[story] = $_SESSION[settings][story];



// set up theme, header,footer and navlinks
if ($_REQUEST[site]) {						// we are in a site
	
	$thisSite = new site($_REQUEST[site]);
	$thisSite->fetchFromDB();
	$thisSite->buildPermissionsArray();
	
	$site_owner = $thisSite->getField("addedby");
	if ($_REQUEST[theme]) $sid .= "&theme=$_REQUEST[theme]";
	if ($_REQUEST[themesettings]) {$themesettings=urlencode(stripslashes($_REQUEST[themesettings])); $sid.="&themesettings=$themesettings";}
	if ($_REQUEST[nostatus]) $sid .= "&nostatus=1";
	if ($_REQUEST[themepreview]) $sid .= "&themepreview=1";
	if (!isset($theme)) $theme = $thisSite->getField("theme");
	if (!isset($themesettings)) $themesettings = $thisSite->getField("themesettings");

	$siteheader = "<div align=center style='margin-bottom: 3px'>";
	$siteheader .= $thisSite->getField("header");
	$siteheader .= "</div>";

	$sitefooter = "<center>";
	$sitefooter .= $thisSite->getField("footer");
	$sitefooter .= "</center>";

}
if ($_REQUEST[section]) {
	$thisSection = new section($thisSite->name,$_REQUEST[section]);
	$thisSection->fetchFromDB();
	$thisSection->buildPermissionsArray();
}
if ($_REQUEST[page]) {
	$thisPage = new page($thisSite->name,$thisSection->id,$_REQUEST[page]);
	$thisPage->fetchFromDB();
	$thisPage->buildPermissionsArray();
}

// compatibility:
if (isset($_REQUEST[action])) $action = $_REQUEST[action];

// if we don't already have content (probably login error messages), then output some shite
if (!$loginerror) {
	$try = "$action.$_SESSION[ltype].inc.php";			// first try a ltype-specific action file
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
if ($thisSection) $sn = " &gt; <a href='$PHP_SELF?$sid&action=$t&site=$_REQUEST[site]&section=$_REQUEST[section]' class='navlink'>".$thisSection->getField("title")."</a>";
if ($thisPage) $pn = " &gt; <a href='$PHP_SELF?$sid&action=$t&site=$_REQUEST[site]&section=$_REQUEST[section]&page=$_REQUEST[page]' class='navlink'>".$thisPage->getField("title")."</a>";
if ($thisSite) {
	$nav = "<a href='$PHP_SELF?$sid&action=$t&site=$_REQUEST[site]' class='navlink'>".$thisSite->getField("title")."</a>";
	$title = $thisSite->getField("title");
}
$nav .= $sn.$pn;
if ($nav) {
	//$siteheader = $siteheader . "<div align=left style='margin-bottom: 5px; margin-left: 10px'>$nav</div>";
	$sitecrumbs = "<div align=left style='margin-bottom: 5px; margin-left: 10px; font-size: 9px'>$nav</div>";
}

// Load non-pervasive theme for "program" actions
// the theme and settings are defined in the config.inc.php

if (!$pervasivethemes && in_array($action,$pervasiveActions)) {
	$theme = $programtheme;
	$themesettings = $programthemesettings;
}

// if there isn't any other theme set, use the default theme
if (!$theme) {
	$theme = $defaulttheme;
	$themesettings = $defaultthemesettings;
}

//output a meta tag
//print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// decode themesettings
if ($themesettings) $themesettings = decode_array($themesettings);

//output the HTML
include("$themesdir/$theme/output.inc.php");

// ------------------
// if register_globals is off, we have to do some hacking to get things to work:
if (!ini_get("register_globals")) {
	$_ign = array("editors","obj","settings","siteObj","sectionObj","pageObj","storyObj",
				"auser","lpass","afname","aemail","atype","amethod","lmethod","ltype",
				"lemail","lfname","luser","lid","aid");
	foreach (array_keys($_SESSION) as $n) { if (!in_array($n,$_ign)) $_SESSION[$n] = $$n; }
}

// debug output -- handy :)
/* print "<pre>"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
print "</pre>";

?>