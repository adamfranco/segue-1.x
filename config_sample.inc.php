<? /* $Id$ */

/******************************************************************************
 * Segue Config File
 ******************************************************************************/



/******************************************************************************
 * *** GLOBAL CONFIGURATION OPTIONS
 *
 * these options pertain to global settings for your copy of Segue
 ******************************************************************************/

	/******************************************************************************
	 * inst_name - the name of your institute (ie, 'Middlebury College')
	 ******************************************************************************/
	$cfg[inst_name] = "";						// "The Best Institute"
	
	/******************************************************************************
	 * full_uri - Segue's full URL path (ie, http://segue.middlebury.edu)
	 ******************************************************************************/
	$cfg[full_uri] = $_full_uri = "";			// "http://segue.middlebury.edu"
	
	/******************************************************************************
	 * inst_ips - an array of IP's to be checked against a user's to decide
	 * 			  if they are within your institution's network
	 *		ex:		= array("140.233.", "192.168.1.");
	 ******************************************************************************/
	$cfg[inst_ips] = array();					// array("192.168.","10.0.0.")
	
	/******************************************************************************
	 * uploaddir - the local folder where userfiles are kept
	 * uploadurl - the URL equivalent of the above folder
	 ******************************************************************************/
	$cfg[uploaddir] = $uploaddir = "";			// "/web/storage/segue"
	$cfg[uploadurl] = $uploadurl = "";			// "http://www.myinstitute.edu/storage/segue"
	
	/******************************************************************************
	 * userdirlimit - the maximum size of any site's media folder (in bytes)
	 *					5242880 = 5MB
	 ******************************************************************************/
	$cfg[userdirlimit] = $userdirlimit = 5242880;
	
	/******************************************************************************
	 * 		Content for the Login Screen
	 * defaulttitle - the title of the page (default, Segue)
	 * defaultmessage - the message to be displayed. can contain HTML
	 * defaultlinks - an associative array of links to display on the left navigation
	 *				  bar for quick access to other sites in your institution
	 ******************************************************************************/
	$cfg[defaulttitle] = $defaulttitle = "Segue";
	$cfg[defaultmessage] = $defaultmessage = "<p>Welcome to Segue! To access your personal and class websites, please login with your username and password above.</p><p>With Segue, you can quickly and easily create websites for yourself and, if you are a professor, for your classes.</p>";
	$cfg[defaultlinks] = $defaultlinks = array(
		"Educational Technology"=>"segue.middlebury.edu/sites/ET",
		"Academic Programs"=>"www.middlebury.edu/academics/",
		"Libraries"=>"www.middlebury.edu/~lib/",
		"Middlebury College"=>"www.middlebury.edu"
	);
	
	/******************************************************************************
	 * defaulttheme - the theme used for non site-specific pages (ie, login page)
	 * defaultthemesettings - an encoded array of the theme's settings. blank will do
	 ******************************************************************************/
	$cfg[defaulttheme] = $defaulttheme = "program";
	$cfg[defaultthemesettings] = $defaultthemesettings = "";
	
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
 * *** AUTHENTICATION
 *
 * these options are specific to user authentication
 ******************************************************************************/

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
	 *		below. you will probably only use DB authentication.
	 ******************************************************************************/
	$cfg[auth_mods] = $_auth_mods = array("db");
	
	/******************************************************************************
	 * MODULE CONFIGUATION
	 *
	 * these options pertain to specific authentication modules
	 * you don't need to change the options for any module you are not using
	 ******************************************************************************/
	
		/******************************************************************************
		 * DB AUTHENTICATION - REQUIRED
		 *
		 * dbhost				the hostname of the sql server (ie, sql.middlebury.edu)
		 * dbuser				the user to connect as (ie, segue)
		 * dbpass				the above user's passwd. may be blank
		 * dbdb					the name of the database to use (ie, segue)
		 ******************************************************************************/
		$cfg[dbhost] = $dbhost = "";					// "sql.myinstitute.edu"
		$cfg[dbuser] = $dbuser = "";					// "segue"
		$cfg[dbpass] = $dbpass = "";					// "secret"
		$cfg[dbdb] = $dbdb = "";						// "segue"
		
		/******************************************************************************
		 * LDAP AUTHENTICATION
		 *
		 * ldapserver				the LDAP server's hostname (ie, ldap.middlebury.edu)
		 * ldap_voadmin_user		an LDAP user that has at least view-only admin privileges
		 * ldap_voadmin_pass		the above user's password
		 ******************************************************************************/
		$cfg[ldapserver] = $ldapserver = "";				// "ldap.myinsitute.edu"
		$cfg[ldap_voadmin_user] = $ldap_voadmin_user = "";	// "jdoe"
		$cfg[ldap_voadmin_pass] = $ldap_voadmin_pass = "";	// "secret"
		
		/******************************************************************************
		 * PAM AUTHENTICATION
		 *
		 * pam_email_suffix				the hostname to be used for user's email address
		 *								creation (ie, user@<pam_email_suffix>)
		 ******************************************************************************/
		$cfg[pam_email_suffix] = $pam_email_suffix = "";	// "myinstitute.edu"







/******************************************************************************
 * OTHER OPTIONS
 *
 * NOTE: THERE IS A 1/1000 CHANCE THAT YOU WILL NEED TO CHANGE THESE
 ******************************************************************************/





	
	
	
	
	/******************************************************************************
	 * $debug: set this to 1 if you would like all debug output from dbwrapper to be printed to the browser.
	 *		possible values: 1, 0
	 *
	 *    NOTE: You PROBABLY (definitely) don't want this unless you're crazy.
	 ******************************************************************************/
	$debug = 0;
	
	/******************************************************************************
	 * Network - the name of the network you are on. specifies what class functions
	 *				are to be used. will be phased out eventually
	 *
	 * DON'T USE THIS OPTION, for now
	 ******************************************************************************/
	$cfg[network] = $_network = "none";
	
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
	 * Semester definitions
	 ******************************************************************************/
	$_semesters = array("f"=>"Fall",
						"w"=>"J-term",
						"s"=>"Spring",
						"l"=>"LS"
	);
	
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
						
	/******************************************************************************
	 * themesdir - the folder where all the themes are kept. shouldn't need changing
	 ******************************************************************************/
	$cfg[themesdir] = $themesdir = "themes";