<? // includes for SitesDB scripts

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

// include the appropriate class functions for this network (stored in $_network)
$_f = "class_functions/" . $_network . ".inc.php";
if (file_exists($_f)) include($_f);
else include("class_functions/empty.inc.php");