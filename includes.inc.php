<? // includes for Segue scripts

if (!ini_get("register_globals")) {
/* 	print "AAAAAAAAAAAAAH!!!<BR><BR>"; */
/* 	print "<b>SUPER DUPER ERROR!</b><BR>"; */
/* 	print "This script can only be run with <b>register_globals</b> turned <b>On</b> in the php configuration!<BR>"; */
/* 	print "You must turn this on before anything will work correctly. Maybe someday we'll re-write it. Maybe."; */
	// a workaround for register_globals = off
//	print "Globals are <b>OFF</b>";
	import_request_variables("gp");
	foreach (array_keys($_SESSION) as $n) {$$n = $_SESSION[$n];}
//	if ($_SESSION["settings"]) print_r($_SESSION["settings"]);
}

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
include("error.inc.php");
include("themes/themeslist.inc.php");
include("dates.inc.php");
include("help/include.inc.php");
include("permissions.inc.php");


//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// now, we need to check if the site people are trying to view needs authentication or not
// if so, we have to display a page that tells people they need to log in
// - there are some pages that require users to be authenticated, no matter what
// - other pages will only require users to be authenticated if the site creator has specified it like that
// - other pages can be either authenticated or not
// --- this functionality will be handled by authentication.inc.php
include("authentication.inc.php");

// include the appropriate class functions for this network (stored in $_network)
$_f = "class_functions/" . $_network . ".inc.php";
if (file_exists($_f)) include($_f);
else include("class_functions/empty.inc.php");