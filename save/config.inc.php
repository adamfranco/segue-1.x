<?
// set the $sid var for easy use
$sid = SID;

// what kind of error reporting do we want? (how cluttered do we want our site to be?)
//error_reporting(0);				// none
//error_reporting(E_ERROR);		// only fatal errors
error_reporting(E_ERROR | E_WARNING);	// more errors
//error_reporting(E_ERROR | E_WARNING | E_NOTICE); 		// lots of errors
//error_reporting(E_ALL);			// all errors !!
// NOTE: the last two options here enable so much error reporting that they will
// seriously screw up MOTS's functionality with HTML forms. Just don't use them.

// these are our database variables
$dbhost = "badger.middlebury.edu";
$dbuser = "sitesdb";
$dbpass = "sitesdb#%&";
$dbdb = "sitesdb";

$ldapserver = "tiger.middlebury.edu";
$ldap_voadmin_user = "fjones";
$ldap_voadmin_pass = "lk87df";

// Segue full URI
$_full_uri = "http://et.middlebury.edu/sitesdb";

// folder where themes are kept
$themesdir = "themes";

//folder for data uploads (images and files)
$uploaddir = "/www/sitesdb/userfiles";
$uploadurl = "userfiles";

/* // some other globals */
/* $motsemail="MOTS <nobody@middlebury.edu>"; */
/* $motsurl="http://sqlserver.middlebury.edu/mots"; */
/* $motsadmincontact="Alex Chapin <achapin@middlebury.edu>"; */

// ***************** you probably don't need to edit below this line ************* //
// ******************************************************************************* //

// these are the possible colors for the bgcolor for classes
$bgcolors = array('AA6666','336633','225588','666633','000000','FFFFFF','CCCC99');

//months
$months = array("January","February","March","April","May","June","July","August","September","October","November","December");


// media types

$mtypes = array(
'image'=>'Image File',
'av'=>'Audio/Video',
'html'=>'HTML page',
);

// defines
define("leftnav","leftnav",TRUE);
define("rightnav","rightnav",TRUE);
define("topnav","topnav",TRUE);

// available templates
$_templates = array("template1"=>"Extensive Course Site",
					"template2"=>"Standard Course Site",
					"template3"=>"Brief Course Site");

?>
