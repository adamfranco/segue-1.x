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
$dbhost = "etdev.middlebury.edu";
$dbuser = "sitesdb";
$dbpass = "sitesdb#%&";
$dbdb = "sitesdb";

/* $dbhost = "sqlserver.middlebury.edu"; */
/* $dbuser = "coursesdb"; */
/* $dbpass = "coursesdb#%&"; */
/* $dbdb = "coursesdb"; */

// LDAP settings
$useldap = 1;	// Set to 1 if you want to use ldap, 0 otherwise
$ldapserver = "tiger.middlebury.edu";
$ldap_voadmin_user = "fjones";
$ldap_voadmin_pass = "lk87df";


// SitesDB full URI
$_full_uri = "http://etdev.middlebury.edu/sitesdb";

// folder where themes are kept
$themesdir = "themes";

//folder for data uploads (images and files)
$uploaddir = "/web/sitesdb_userfiles";
$uploadurl = "/sitesdb_userfiles";

// Theme for login/default pages
$defaulttheme = "program";
$defaultthemesettings = "";

// Pervasive/non-pervasive theme settings
$pervasivethemes = 0;	// Set to 1 to make the editing windows take on the site, 0 for special program theme
$programtheme = "program";
$programthemesettings = "";


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
$_templates = array("template0"=>"Default",
					"template1"=>"Extensive Course Site",
					"template2"=>"Standard Course Site",
					"template3"=>"Brief Course Site");

?>
