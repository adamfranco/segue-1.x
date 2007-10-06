<? /* $Id$ */

// set the $sid var for easy use
// $sid = SID;

//defines - DO NOT CHANGE THESE!!!
define("leftnav","leftnav",TRUE);
define("rightnav","rightnav",TRUE);
define("topnav","topnav",TRUE);

$myDir = dirname(__FILE__)."/";
require_once($myDir."functions.inc.php");
require_once($myDir."config.inc.php");
require_once($myDir."config_defaults.inc.php");
require_once($myDir."dbwrapper.inc.php");
require_once($myDir."error.inc.php");
require_once($myDir."themes/themeslist.inc.php");
require_once($myDir."dates.inc.php");
require_once($myDir."help/include.inc.php");
require_once($myDir."permissions.inc.php");
require_once($myDir."htmleditor/editor.inc.php");

// Define a constant so that we can test for proper application flow (including
// this file and other configs).
// One potential attack is to directly call a sub-include, bypassing defining of
// config parameters and overriding them with values from request if register_globals
// is on. Checking for this constant can test for propper flow.
if (!defined("CONFIGS_INCLUDED"))
	define ("CONFIGS_INCLUDED", true);



// now, we need to check if the site people are trying to view needs authentication or not
// if so, we have to display a page that tells people they need to log in
// - there are some pages that require users to be authenticated, no matter what
// - other pages will only require users to be authenticated if the site creator has specified it like that
// - other pages can be either authenticated or not
// --- this functionality will be handled by authentication.inc.php
require_once($myDir."authentication.inc.php");

// include the appropriate class functions for this network (stored in $_network)
if (!preg_match('/^[a-z_0-9]+$/i', $_network))
		die ('Error: invalid network, '.$_network.'.');
$_f = $myDir."class_functions/" . $_network . ".inc.php";

if (file_exists($_f)) {
	require_once($_f);
} else {
	require_once($myDir."class_functions/empty.inc.php");
}
