<? /* $Id$ */


/******************************************************************************
 * $debug: set this to 1 if you would like all debug output from dbwrapper to be printed to the browser.
 *		possible values: 1, 0
 *
 *    NOTE: You PROBABLY (definitely) don't want this unless you're crazy.
 ******************************************************************************/
$debug = 0;

/******************************************************************************
 * Institute Name - the name of your institute (ie, 'Middlebury College')
 ******************************************************************************/
$cfg[inst_name] = "The Best Institute";

/******************************************************************************
 * inst_ips - an array of IP's to be checked against a user's to decide
 * 			  if they are within your institution
 *		ex:		= array("140.233.", "192.168.1.");
 ******************************************************************************/
$cfg[inst_ips] = array("192.168.","10.0.0.");

/******************************************************************************
 * Network - the name of the network you are on. specifies what class functions
 *				are to be used. will be phased out eventually
 *
 * DON'T USE THIS OPTION UNLESS
 ******************************************************************************/
$cfg[network] = $_network = "midd";

/******************************************************************************
 * full_uri - Segue's full URL path (ie, http://segue.middlebury.edu)
 ******************************************************************************/
$cfg[full_uri] = $_full_uri = "http://segue.middlebury.edu";


/******************************************************************************
 * AUTHENTICATION MODULES
 *	specify what authentication systems you wish to use and in what order
 * choices are: db 		(REQUIRED -- sql database)
 *				ldap	(LDAP server)
 *						requires ldap.so PHP module
 *				pam		(UNIX PAM - Pluggable Authentication Module)
 *						requires pam_auth.so PHP module
 *
 *	NOTE: for any you choose, be sure to set the correct configuration options
 *		below.
 ******************************************************************************/
$cfg[auth_mods] = $_auth_mods = array("db");

/******************************************************************************
 * DB AUTHENTICATION - REQUIRED
 *
 * dbhost				the hostname of the sql server (ie, sql.middlebury.edu)
 * dbuser				the user to connect as (ie, segue)
 * dbpass				the above user's passwd. may be blank
 * dbdb					the name of the database to use (ie, segue)
 ******************************************************************************/
$cfg[dbhost] = $dbhost = "sql.myinstitute.edu";
$cfg[dbuser] = $dbuser = "segue";
$cfg[dbpass] = $dbpass = "secret";
$cfg[dbdb] = $dbdb = "segue";


/******************************************************************************
 * LDAP AUTHENTICATION
 *
 * ldapserver				the LDAP server's hostname (ie, ldap.middlebury.edu)
 * ldap_voadmin_user		an LDAP user that has at least view-only admin privileges
 * ldap_voadmin_pass		the above user's password
 ******************************************************************************/
$cfg[ldapserver] = $ldapserver = "ldap.myinsitute.edu";
$cfg[ldap_voadmin_user] = $ldap_voadmin_user = "jdoe";
$cfg[ldap_voadmin_pass] = $ldap_voadmin_pass = "secret";


/******************************************************************************
 * PAM AUTHENTICATION
 *
 * pam_email_suffix				the hostname to be used for user's email address
 *								creation (ie, user@<pam_email_suffix>)
 ******************************************************************************/
$cfg[pam_email_suffix] = $pam_email_suffix = "myinstitute.edu";


/******************************************************************************
 * themesdir - the folder where all the themes are kept. shouldn't need changing
 ******************************************************************************/
$cfg[themesdir] = $themesdir = "themes";


/******************************************************************************
 * uploaddir - the local folder where userfiles are kept
 * uploadurl - the URL equivalent of the above folder
 ******************************************************************************/
$cfg[uploaddir] = $uploaddir = "/www/segue_userfiles";
$cfg[uploadurl] = $uploadurl = "/segue_userfiles";

/******************************************************************************
 * userdirlimit - the maximum size of any site's media folder (in bytes)
 *					5242880 = 5MB
 ******************************************************************************/
$cfg[userdirlimit] = $userdirlimit = 5242880;

/******************************************************************************
 * defaulttheme - the theme used for non site-specific pages (ie, login page)
 * defaultthemesettings - an encoded array of the theme's settings. blank will do
 ******************************************************************************/
$cfg[defaulttheme] = $defaulttheme = "program";
$cfg[defaultthemesettings] = $defaultthemesettings = "";

/******************************************************************************
 * pervasivethemes - 1 to use site's theme for add/edit pages
 *					 0 to use $cfg[programtheme]
 * programtheme	- theme to use when pervasivethemes is set to 0
 * programthemesettings - encoded array of programtheme's settings. blank will do
 ******************************************************************************/
$cfg[pervasivethemes] = $pervasivethemes = 0;
$cfg[programtheme] = $programtheme = "program";
$cfg[programthemesettings] = $programthemesettings = "";

/******************************************************************************
 * allowpersonalsites - 1 to allow add/edit of personal sites
 * allowclasssites - 1 to allow add/edit of class-specific sites
 * 
 * 		Some institutions may want to limit functionality to either or. One of
 *		the two must be selected. Some institutions also create two instances
 *		of Segue to better organize their sites for end users. Both instances
 *		can use the same database data.
 * 
 * personalsitesurl - the URL of your personal sites instance of Segue
 * classsitesurl - the URL of your class sites instance of Segue
 ******************************************************************************/
$cfg[allowpersonalsites] = $allowpersonalsites = 1;
$cfg[allowclasssites] = $allowclasssites = 1;
$cfg[personalsitesurl] = $personalsitesurl = "";
$cfg[classsitesurl] = $classsitesurl = "";

/******************************************************************************
 * 		Content for the Login Screen
 * defaulttitle - the title of the page (default, Segue)
 * defaultmessage - the message to be displayed. can contain HTML
 * defaultlinks - an associative array of links to display on the left navigation
 *				  bar for quick access to other sites in your institution
 ******************************************************************************/
$cfg[defaulttitle] = $defaulttitle = "Segue";
$cfg[defaultmessage] = $defaultmessage = "<p>Welcome to Segue! To access your personal and class websites, please login with your username and password above. For Middlebury College users, these are identical to your email username and password.  See:<br><a href=http://www.middlebury.edu/webemailtools.html target=new_window>http://www.middlebury.edu/webemailtools.html</a></p><p>With Segue, you can quickly and easily create websites for yourself and, if you are a professor, for your classes.</p><hr size=1><font class=small>If you are not affliated with Middlebury College and you would like a demo of Segue, log in above as follows:<br>username: demo<br>password: demo<br><br>For more information, contact: <a href='mailto:achapin@middlebury.edu'>achapin@middlebury.edu</a></font>";
$cfg[defaultlinks] = $defaultlinks = array(
	"Educational Technology"=>"segue.middlebury.edu/sites/ET",
	"Academic Programs"=>"www.middlebury.edu/academics/",
	"Libraries"=>"www.middlebury.edu/~lib/",
	"Middlebury College"=>"www.middlebury.edu"
);

/******************************************************************************
 * Semester definitions
 ******************************************************************************/

$_semesters = array("f"=>"Fall",
					"w"=>"J-term",
					"s"=>"Spring",
					"l"=>"LS"
);

/******************************************************************************
 * 	YOU DO NOT NEED TO EDIT BELOW THIS LINE.... but you may
 ******************************************************************************/

//months
$cfg[months] = $months = array("January","February","March","April","May","June","July","August","September","October","November","December");


/******************************************************************************
 * templates - associative array of template sites and their names
 ******************************************************************************/
$cfg[templates] = $_templates = array(
					"template0"=>"Default",
					"template1"=>"Extensive Course Site",
					"template2"=>"Standard Course Site",
					"template3"=>"Brief Course Site");

?>
