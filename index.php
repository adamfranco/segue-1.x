<?	// index.php for coursesDB module
	// this file controls pretty much the entire program, taking input and executing the correct scripts accordingly

ob_start();		// start the output buffer so we can use headers if needed
session_start();// start the session manager :) -- important, as we just learned

if (ereg("^login",$QUERY_STRING)) {
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


include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
include("error.inc.php");
include("themes/themeslist.inc.php");
include("dates.inc.php");
include("help/include.inc.php");

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// now, we need to check if the site people are trying to view needs authentication or not
// if so, we have to display a page that tells people they need to log in
// - there are some pages that require users to be authenticated, no matter what
// - other pages will only require users to be authenticated if the site creator has specified it like that
// - other pages can be either authenticated or not
// --- this functionality will be handled by authentication.inc.php
include("authentication.inc.php");

// if we are logged in, get a list of classes the user has
// but only if login method was LDAP.. otherwise don't waste the time
$classes=array();
$oldclasses=array();
$futureclasses=array();
$oldsites=array();

if ($_loggedin) {

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

include("permissions.inc.php");

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
	$sitefooter = "<center>".stripslashes(urldecode($siteinfo[footer]))."</center>";
	$siteheader = "<div align=center style='margin-bottom: 3px'>".stripslashes(urldecode($siteinfo[header]))."</div>";
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


// decode the arrays -- unneeded! (idiot me)
/*
$topnav = decode_array($topnav);
$leftnav = decode_array($leftnav);
$rightnav = decode_array($rightnav);
*/


//output a meta tag
//print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// decode themesettings
if ($themesettings) $themesettings = decode_array($themesettings);
//print "$themesdir/$theme/";



//output the HTML
include("$themesdir/$theme/output.inc.php");

?>