<? /* $Id$ */

	// this file controls pretty much the entire program, taking input and executing the correct scripts accordingly

// we need to include object files before session_start() or registered
// objects will be broken.
include("objects/objects.inc.php");


ob_start();		// start the output buffer so we can use headers if needed

// we need to include the config before we start the session 
if (!file_exists("config.inc.php"))
	die ("<h4>ERROR! You must create a config file before <b>Segue</b> can run.</h4>
		Copy the 'config_sample.inc.php' in your segue directory to 'config.inc.php' 
		and edit the values there to point to your directories, url, and database.");
require_once("config.inc.php");
require_once("config_utils.inc.php");
checkConfig();

require_once("auth_not_req_actions.inc.php");

// check if we are only allowing access to one site based on virtual host
if (isset($cfg["vhosts"]) && count($cfg["vhosts"])) {
//      print "checking vhosts...";
        // we are getting the following information: host, site, full_uri, show_statusbar
        $currentHost = $_SERVER["SERVER_NAME"];
//      print " current host is '$currentHost'<br />";
        foreach ($cfg["vhosts"] as $vhost) {
//              print "checking config host ".$vhost["host"]."<br />";
                if ($vhost["host"] == $currentHost) {
                        if ($vhost["show_status"] == false) 
                        	$_REQUEST["nostatus"] = "1";
                        
                        $_REQUEST["site"] = $vhost["site"];
                        $cfg["full_uri"] = $_full_uri = $vhost["full_uri"];
                        
                        // All of the "home" links in Segue doen't specify an action,
                        // so they will be redirected to the site page.
                        // putting the if-statement allows normal editing and other
                        // functions to work, while still retricting to the same site.
                        if (!$_REQUEST["action"])
                        	$_REQUEST["action"] = "site";
                        	
                        break;
                }
        }
}

// We don't want to force the cookie domain if we are not accessing segue
// from the domain specified (either due to a configuration error or a 
// vhost setting) as that would make the cookie inaccessible.
if ($cfg[domain] && ereg($cfg['domain'], $_SERVER["SERVER_NAME"]))
	ini_set("session.cookie_domain",$cfg[domain]);
	
//ini_set("session.name","SeguePHPSESSID");
session_start();// start the session manager :) -- important, as we just learned

header("Content-type: text/html; charset=utf-8");

if (ereg("^login",getenv("QUERY_STRING"))) {

	if (session_id()) {
		// clear only our session variables as to not interfere with other apps
		$vars = array("luser","auser",
					  "lemail","aemail",
					  "lid","aid",
					  "lfname","afname",
					  "ltype","atype",
					  "lmethod","amethod",
					  "settings","obj","editors","siteObj","origSiteObj","sectionObj","pageObj","storyObj");
		foreach ($vars as $var) {
			if (ini_get("register_globals")) session_unregister($var);
			unset($_SESSION[$var]);
		}
//		session_unset();
//		session_destroy();
	}
	
	// --------- upon logout, send the user back where they were if possible -----------
	$getVars = "";
	
	// common thing is to logout while editing a site. Just translate that to
	// the "site" action to avoid confusing people.
	if ($_GET['action'] == "viewsite")
		$_GET['action'] = "site";
	
	// Make sure that we can stay at our current action. If not, send to
	// the default page.	
	if ($_GET['action'] && in_array($_GET['action'], $actions_noauth)) {
		foreach ($_GET as $key => $val) {
			$getVars .= "&".$key."=".$val;
		}
	}
	header("Location: index.php?".$getVars);
}

// actions for which we use pervasive themes (if enabled)
// and actions for which certain session variables are allowed (settings, *Obj)
$pervasiveActions = array("edit_site","add_site","add_section","edit_section","add_page","edit_page","add_story","edit_story");
$permittedSettingsActions = $pervasiveActions;
$permittedSettingsActions[]='site';
$permittedPermissionsActions = array("site","viewsite");

// check if we have any stray session variables
if (!in_array($_REQUEST[action],$permittedSettingsActions)) {
	if ($_SESSION[settings] || $_SESSION[obj] || $_SESSION[editors] || $_SESSION[siteObj] || $_SESSION[origSiteObj] || $_SESSION[sectionObj] || $_SESSION[pageObj] || $_SESSION[storyObj]) {
		if (ini_get("register_globals")) {
			session_unregister("settings");
			session_unregister("obj");
			session_unregister("editors");
			session_unregister("siteObj");
			session_unregister("origSiteObj");
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

// initialize the content variables
$leftnav = $rightnav = $topnav = $topnav2 = $leftnav2 = array();
$content= "";
$leftnav_extra = $rightnav_extra = $topnav_extra = '';

if (!$action) { $action="default"; }		// if there's no action, use default
if (ereg("\.",$action)) $action="no_action";	// security to prevent someone from setting an action to add_user.admin or something (maybe unneeded, but can't hurt)

// include all necessary files
include("includes.inc.php");

//echo "<pre>";

// if we are logged in, get a list of classes the user has
// but only if login method was LDAP.. otherwise don't waste the time
$classes=array();
$oldclasses=array();
$futureclasses=array();
$oldsites=array();

/* --------------- eventually, this command will be gone... unneeded and handled by ADOdb */
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);


// ------ Build arrays of all classes and sites ----------
if ($_loggedin) {
	// below if statement should be changed to check a config variable that states
	// if classes should be check, and what routine to use to get that	
	$classes = getuserclasses($auser,"now");
	$oldclasses = getuserclasses($auser,"past");
	$futureclasses = getuserclasses($auser,"future");
		
	// one array containing all user's classes
	$allclasses[$_SESSION['auser']] = array_merge($classes,$oldclasses,$futureclasses);
	
	// Sort the classes arrays only if that is needed, IE, on the default page.
		
	// get other sites they have added, but which aren't in the classes list
	if ($all_sites = segue::getAllSites($_SESSION[auser])) {
		foreach ($all_sites as $n) {
/* 			$n = $a['name']; */
			if (!is_array($allclasses[$_SESSION['auser']][$n]) && isclass($n)) {
				$oldsites[]=$n;
			}
		}
	}
}


// if we have info stored in settings session var, get some of it
if ($_SESSION[settings][site]) $_REQUEST[site] = $_SESSION[settings][site];
if ($_SESSION[settings][section]) $_REQUEST[section] = $_SESSION[settings][section];
if ($_SESSION[settings][page]) $_REQUEST[page] = $_SESSION[settings][page];
if ($_SESSION[settings][story]) $_REQUEST[story] = $_SESSION[settings][story];

if ($cfg['user_notice']) {
	print "<div style='border: 4px solid red; font-size: large;'>";
	print $cfg['user_notice'];
	print "</div>";
}

// set up theme, header,footer and navlinks
if ($_REQUEST[site]) {						// we are in a site
	
	$thisSite =& new site($_REQUEST[site]);
	$thisSlot =& new slot($thisSite->name);
	$thisSite->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen($_REQUEST[section],$_REQUEST[page]);
//	$thisSite->buildPermissionsArray(1,1);
	
	$site_owner = $thisSlot->getField("owner");
	if ($_GET[theme]) $sid .= "&theme=$_REQUEST[theme]";
	if ($_GET[themesettings]) {$themesettings=urlencode(stripslashes($_REQUEST[themesettings])); $sid.="&themesettings=$themesettings";}
	if ($_REQUEST[nostatus]) $sid .= "&nostatus=1";
	if ($_REQUEST[themepreview]) $sid .= "&themepreview=1";
	if (!isset($theme)) $theme = $thisSite->getField("theme");
	if (!isset($themesettings)) $themesettings = $thisSite->getField("themesettings");

	$siteheader = "<div align='center' style='margin-bottom: 3px'>";
	
	/******************************************************************************
	 * replace general media library urls (i.e. $mediapath/$sitename/filename)
	 ******************************************************************************/
	$mod_header = convertTagsToInteralLinks($_REQUEST[site], $thisSite->getField("header"));
	$mod_footer = convertTagsToInteralLinks($_REQUEST[site], $thisSite->getField("footer"));
	
	$siteheader .= $mod_header;	
	//$siteheader .= $thisSite->getField("header");
	$siteheader .= "</div>";

	$sitefooter = "<center>";
	$sitefooter .= $mod_footer;
	//$sitefooter .= $thisSite->getField("footer");
	$sitefooter .= "</center>";

}
if ($_REQUEST[section]) {
//	$thisSection =& new section($thisSite->name,$_REQUEST[section],&$thisSite);
	$thisSection =& $thisSite->sections[$_REQUEST[section]];
//	$thisSection->fetchFromDB();
//	$thisSection->buildPermissionsArray();
}
if ($_REQUEST[page]) {
//	$thisPage =& new page($thisSite->name,$thisSection->id,$_REQUEST[page],&$thisSection);
	$thisPage =& $thisSite->sections[$_REQUEST[section]]->pages[$_REQUEST[page]];
//	$thisPage->fetchFromDB();
//	$thisPage->buildPermissionsArray();
}

// compatibility:
if (isset($_REQUEST[action])) $action = $_REQUEST[action];
//print"ok"; exit();

// if we don't already have content (probably login error messages), then output some shite
if (!$loginerror) {
	
	$try = "$action.$_SESSION[ltype].inc.php";			// first try a ltype-specific action file
	if (file_exists($try)) {					// yes, indeed
		include($try);
	} else {
		$try = "$action.inc.php";				// try a general one
		if (file_exists($try)) {
			//print $action; exit();
			include($try);
			//print $action; exit();
		}
		else include("no_action.inc.php");		// action not implemented yet or doesn't exist :(
	}
}

//echo "</pre>";

// output any errors that may exist to the content variables
printerr();

/****************************************************************************/
/*					use content vars from above and print out content		*/

$t = $action;
if ($t != 'site') $t = 'viewsite';
if ($thisSite) $st = ($thisSite->getField("type")=='publication')?"$t&supplement=listarticles":$t;
if ($thisSection) $sn = " &gt; <a href='$PHP_SELF?$sid&action=$st&site=$_REQUEST[site]&section=$_REQUEST[section]' class='navlink'>".$thisSection->getField("title")."</a>";
if ($thisPage) $pn = " &gt; <a href='$PHP_SELF?$sid&action=$t&site=$_REQUEST[site]&section=$_REQUEST[section]&page=$_REQUEST[page]' class='navlink'>".$thisPage->getField("title")."</a>";
if ($thisSite) {
	$nav = "<a href='$PHP_SELF?$sid&action=$t&site=$_REQUEST[site]' class='navlink'>".$thisSite->getField("title")."</a>";
	$title = $thisSite->getField("title");
}
$nav .= $sn.$pn;
if ($nav) {
	$sitecrumbs = "<div align='left' style='margin-bottom: 5px; margin-left: 10px; font-size: 9px'>$nav</div>";
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
	foreach (array_keys($_SESSION) as $n) { if (!in_array($n,$_ign)) $_SESSION[$n] = &$$n; }
}

?>
