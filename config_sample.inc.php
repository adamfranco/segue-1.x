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
	$cfg['inst_name'] = "";						// "The Best Institute"
	
	/******************************************************************************
	 * full_uri - Segue's full URL path (ie, http://segue.middlebury.edu)
	 *		Don't put a slash "/" at the end of the url!
	 ******************************************************************************/
	$cfg['full_uri'] = $_full_uri = "";			// "http://segue.middlebury.edu"
	
	/******************************************************************************
	 * inst_ips - an array of IP's to be checked against a user's to decide
	 * 			  if they are within your institution's network
	 *		ex:		= array("140.233.", "192.168.1.");
	 ******************************************************************************/
	$cfg['inst_ips'] = array();					// array("192.168.","10.0.0.")
	
	/******************************************************************************
	 * domain - The domain under which Segue runs. This is useful if you have multiple
	 * 			instances of segue running under different subdomains and you want
	 * 			them to share authentication informatin. This field is used for
	 * 			setting the cookie domain for session data. 
	 ******************************************************************************/
	$cfg['domain'] = "";					// "mydomain.com"
	
	/******************************************************************************
	 * uploaddir - the local folder where userfiles are kept
	 * uploadurl - the URL equivalent of the above folder
	 ******************************************************************************/
	$cfg['uploaddir'] = $uploaddir = "";			// "/web/storage/segue"
	$cfg['uploadurl'] = $uploadurl = "";			// "http://www.myinstitute.edu/storage/segue"
	
	/******************************************************************************
	 * userdirlimit - the maximum size of any site's media folder (in bytes)
	 *					5242880 = 5MB
	 ******************************************************************************/
	$cfg['userdirlimit'] = $userdirlimit = 5242880;
	
	/******************************************************************************
	 * logexpiration - the number of days to keep segue log entries before they are 
	 *					deleted.
	 ******************************************************************************/
	$cfg['logexpiration'] = 150;
	
	/******************************************************************************
	 * 		Content for the Login Screen
	 * defaulttitle - the title of the page (default, Segue)
	 * defaultmessage - the message to be displayed. can contain HTML
	 * defaultlinks - an associative array of links to display on the left navigation
	 *				  bar for quick access to other sites in your institution
	 * inst_logo_url - the url to the logo that will appear in the upper right of
	 *					the login screen
	 ******************************************************************************/
	$cfg['defaulttitle'] = $defaulttitle = "Segue";
	$cfg['defaultmessage'] = $defaultmessage = "<p>Welcome to Segue! To access your personal and class websites, please login with your username and password above.</p><p>With Segue, you can quickly and easily create websites for yourself and, if you are a professor, for your classes.</p>";
	$cfg['defaultlinks'] = $defaultlinks = array(
		"Educational Technology"=>"segue.middlebury.edu/sites/ET",
		"Academic Programs"=>"www.middlebury.edu/academics/",
		"Libraries"=>"www.middlebury.edu/~lib/",
		"Middlebury College"=>"www.middlebury.edu"
	);
	$cfg['inst_logo_url'] = $inst_logo_url = "themes/program/images/gray/midd.gif";
	
	/******************************************************************************
	 * user_notice - This notice will be displayed in a large red box at the top of
	 * 		every screen, useful for testing messages, etc.
	 ******************************************************************************/
	$cf['user_notice'] = "";
	
	/******************************************************************************
	 * defaulttheme - the theme used for non site-specific pages (ie, login page)
	 * defaultthemesettings - an encoded array of the theme's settings. blank will do
	 ******************************************************************************/
	$cfg['defaulttheme'] = $defaulttheme = "program";
	$cfg['defaultthemesettings'] = $defaultthemesettings = "";
	
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
	 *		Don't put a slash "/" at the end of the url!
	 * classsitesurl - the URL of your class sites instance of Segue
	 *		Don't put a slash "/" at the end of the url!
	 ******************************************************************************/
	$cfg['allowpersonalsites'] = $allowpersonalsites = 1;
	$cfg['allowclasssites'] = $allowclasssites = 1;
	$cfg['personalsitesurl'] = $personalsitesurl = "";
	$cfg['classsitesurl'] = $classsitesurl = "";

	/*****************************************************************************
	 * $cfg['vhosts'] = if you have virtual hosts set up on your server you can
	 *                "bind" a segue site to it, so anyone accessing segue
	 *                from a certain virtual host will only be able to see
	 *                that one site.
	 *
	 *		For this feature to work, you must point the DocumentRoot
	 *		of the virtual host to the segue directory.
	 *
	 * $cfg['vhosts'] = array( // an array of arrays, each describing a virtual host
	 *      array(
	 *              "host"=>        "the domain name of the vhost, ie, www.sample.com",
	 *              "full_uri"=>    "the URL of the site - ie, http://www.sample.com",
	 *              "site"=>        "the short name of the site you want displayed - ie, template0",
	 *              "show_status"=> "display the login/status bar at the top of the
	 *                               window";
	 *      ),
	 * // ... add more vhosts here
	 * ); // done
	 *
	 *****************************************************************************/
	
	$cfg['vhosts'] = array(
	/*
			array(
					"host"=>        "www.sample.com",
					"full_uri"=>    "http://www.sample.com",
					"site"=>        "template0",
					"show_status"=> TRUE
			)
	*/
	);

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
	$cfg['auth_mods'] = $_auth_mods = array("db");
	
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
		$cfg['dbhost'] = $dbhost = "";					// "sql.myinstitute.edu"
		$cfg['dbuser'] = $dbuser = "";					// "segue"
		$cfg['dbpass'] = $dbpass = "";					// "secret"
		$cfg['dbdb'] = $dbdb = "";						// "segue"
		
		/******************************************************************************
		 * LDAP AUTHENTICATION
		 *
		 * ldapserver				the LDAP server's hostname (ie, ldap.middlebury.edu)
		 * ldap_voadmin_user		an LDAP user that has at least view-only admin privileges
		 * ldap_voadmin_pass		the above user's password
		 ******************************************************************************/
		$cfg['ldap_server'] = "";				// "ldap.myinsitute.edu"
		$cfg['ldap_voadmin_user_dn'] = "";	// "cn=jdoe" (include base dn if needed)
		$cfg['ldap_voadmin_pass'] = "";		// "secret"		
		$cfg['ldap_base_dn'] = "";   			// "o=institute,c=country"
		$cfg['ldap_user_dn'] = ""; 			// ou=people

		$cfg['ldap_username_attribute'] = ""; 	// "uid"
		$cfg['ldap_fullname_attribute'] = ""; 	// "cn"
		$cfg['ldap_email_attribute'] = "";		// "mail"
		$cfg['ldap_group_attribute'] = "";		// "memberOf"
		
		$cfg['ldap_prof_groups'] = $ldap_prof_groups = array(
			"All_Faculty",
			"All_Staff"
		);
		
			/******************************************************************************
			 * LDAP COURSE MEMBER INFORMATION
			 *
			 * 
			 ******************************************************************************/

			$cfg['ldap_classgroup_dn'] = "";			// ou=groups,ou=classes
			$cfg['ldap_groupname_attribute'] = "";	// "groupid"
			$cfg['ldap_groupmember_attribute'] = "";	// "member"
		
		/******************************************************************************
		 * PAM AUTHENTICATION
		 *
		 * pam_email_suffix				the hostname to be used for user's email address
		 *								creation (ie, user@<pam_email_suffix>)
		 ******************************************************************************/
		$cfg['pam_email_suffix'] = $pam_email_suffix = "";	// "myinstitute.edu"







/******************************************************************************
 * OTHER OPTIONS
 ******************************************************************************/

	/******************************************************************************
	 * email patterns to block for self registration
	 * Use this option to prevent users already in your authentication system
	 * from accidentically creating additional visitor accounts in publically
	 * accessible discussions.
	 *
	 * Each entry in the array can be a regular expression string -- in the format
	 * used by PHP's ereg() function.
	 ******************************************************************************/

 		$cfg['visitor_email_excludes'] = $visitor_email_excludes = array(
			  // "myinstitute.edu" 
		);
		
	/******************************************************************************
	 * Segue Authentication help
	 * if Segue is your primary means of authentication then turn password reset on
	 * to allow users to self register visitor account then turn auth_registration on
	 * if you use an external authentication system then include help instructions
	 * for creating user accounts
	 ******************************************************************************/
	 
		$cfg['auth_reset_on'] = $auth_reset_on = TRUE;		// displays password reset link
		$cfg['auth_register_on'] = $auth_register_on = TRUE;	// displays visitor registration link
		$cfg['auth_help_on'] = $auth_help_on = TRUE;			// displays custom authentication help info
		$cfg['auth_help'] .= "Users should manage their user accounts at:";
		$cfg['auth_help'] .= " <a href=http://www/mydomain.com/help/users>User Accounts</a>";	

	
	/******************************************************************************
	 * Network - the name of the network you are on. specifies what class functions
	 *				are to be used. will be phased out eventually
	 *
	 ******************************************************************************/
	$cfg['network'] = $_network = "none";
	
		/******************************************************************************
		 * Options if using the "midd" network.
		 *
		 * 
		 ******************************************************************************/
		$cfg['coursefolders_host'] = "";	// myhost.myinstitute.edu
		$cfg['coursefolders_username'] = "";	// jsmith
		$cfg['coursefolders_password'] = "";	// secret
		$cfg['coursefolders_db'] = "";	// coursefolders
		$cfg['coursefolders_table'] = "";	// courses
		$cfg['coursefolders_coursecode_column'] = "";	// code
		$cfg['coursefolders_semester_column'] = "";	// semester
		$cfg['coursefolders_year_column'] = "";	// year
		$cfg['coursefolders_title_column'] = "";	// title
		$cfg['coursefolders_url_column'] = "";	// url
	
	/******************************************************************************
	 * pervasivethemes - 1 to use site's theme for add/edit pages
	 *					 0 to use $cfg['programtheme']
	 * programtheme	- theme to use when pervasivethemes is set to 0
	 * programthemesettings - encoded array of programtheme's settings. blank will do
	 ******************************************************************************/
	$cfg['pervasivethemes'] = $pervasivethemes = 0;
	$cfg['programtheme'] = $programtheme = "program";
	$cfg['programthemesettings'] = $programthemesettings = "";
	
	/******************************************************************************
	 * $debug: set this to 1 if you would like all debug output from dbwrapper to be 
	 * printed to the browser.
	 *		possible values: 1, 0
	 *
	 *    NOTE: You PROBABLY (definitely) don't want this unless you're crazy.
	 ******************************************************************************/
	$cfg['debug'] = $debug = FALSE;
	$cfg['printAllQueries'] = $printAllQueries = FALSE;
	
	/******************************************************************************
	 * Semester definitions
	 *
	 * As few or many semesters as desired can be entered below. The array keys
	 * are the strings that will be stored in the database when a class is 
	 * added to Segue.
	 ******************************************************************************/	
	$cfg['semesters'] = array (
		"w" => array (
			"name" => "Winter",
			"start_month" => "01",
			"start_day" => "01",
			"end_month" => "02",
			"end_day" => "10"
		),
		"s" => array (
			"name" => "Spring",
			"start_month" => "02",
			"start_day" => "11",
			"end_month" => "05",
			"end_day" => "30"
		),
		"l" => array (
			"name" => "Summer",
			"start_month" => "05",
			"start_day" => "30",
			"end_month" => "08",
			"end_day" => "15"
		),
		"f" => array (
			"name" => "Fall",
			"start_month" => "09",
			"start_day" => "01",
			"end_month" => "12",
			"end_day" => "30"
		)
		
	);
	
	//months
	$cfg['months'] = $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
	
	/******************************************************************************
	 * templates - associative array of template sites and their names
	 ******************************************************************************/
	$cfg['templates'] = $_templates = array(
						"template0"=>"Default",
						"template1"=>"Extensive Course Site",
						"template2"=>"Standard Course Site",
						"template3"=>"Brief Course Site",
						"template4"=>"Advanced: Single Section",
						"template5"=>"Advanced: Blank"
						);
						
	/******************************************************************************
	 * themesdir - the folder where all the themes are kept. shouldn't need changing
	 ******************************************************************************/
	$cfg['themesdir'] = $themesdir = "themes";
