<? /* $Id$ */

// set the $sid var for easy use
$sid = SID;

// what kind of error reporting do we want? (how cluttered do we want our site to be?)
//error_reporting(0);				// none
//error_reporting(E_ERROR);		// only fatal errors
error_reporting(E_ERROR | E_WARNING);	// more errors
//error_reporting(E_ERROR | E_WARNING | E_NOTICE); 		// lots of errors
//error_reporting(E_ALL);			// all errors !!
/* NOTE: the last two options here enable so much error reporting that they will */
/* seriously screw up Segue's functionality with HTML forms. Just don't use them */

//defines - DO NOT CHANGE THESE!!!
define("leftnav","leftnav",TRUE);
define("rightnav","rightnav",TRUE);
define("topnav","topnav",TRUE);



if (!ini_get("register_globals")) {
	import_request_variables("gp");
	if (is_array($_SESSION)) foreach (array_keys($_SESSION) as $n) {$$n = $_SESSION[$n];}
	if (is_array($_SERVER)) foreach (array_keys($_SERVER) as $n) {$$n = $_SERVER[$n];}
}

include("functions.inc.php");
include("config.inc.php");
include("dbwrapper.inc.php");
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
