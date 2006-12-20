/******************************************************************************
 * Install instructions
 ******************************************************************************/
1. Install moodle (version 1.6.3)
2. Copy segue/segue_link.php to moodle root
3. Copy segue/segue-moodle_themes.zip to moodle/themes and unzip
(includes: standard, standardlogo, Segue_Bevelbox, Segue_Shadow, Segue_Tornpaper)
4. delete all other directories/files in moodle/themes EXCEPT:
moodle/themes/index.php
moodle/themes/preview.php
moodle/themes/UPGRADE.txt

5. Edit segue_link.php to specify location of linking database 
and Segue url as follows:

$dblink_host = "localhost";
$dblink_user = "test";
$dblink_pass = "test";
$dblink_db = "segue-moodle";
$segue_url = "https://segue.middlebury.edu/sites/";

6. Install segue (version 1.6.x)
7. edit segue/config.inc.php to specify location of linking database
and Moodle url as follows:

$cfg['dbhost_link'] = $dbhost_link = "localhost";
$cfg['dbuser_link'] = $dbuser_link = "test";
$cfg['dbpass_link'] = $dbpass_link = "test";
$cfg['dbdb_link'] = $dbdb_link = "segue-moodle";
$cfg['moodle_url'] = $moodle_url = "http://measure.middlebury.edu/";


/******************************************************************************
 * Moodle Configuration
 ******************************************************************************/

8. Configure Moodle as indicated below
These settings are designed for specifically for using Moodle as follows:
1. Assessments
-Moodle quiz module is well developed and provides functionality most lacking
in Segue
2. LDAP and Visitor registration
-this configuration will set up Moodle so that LDAP users can be authenticated
into Moodle via linked Segue sites
-non LDAP users (i.e. Visitors) can create user accounts using email authentication
which will allow them to log into Moodle and enrol in courses (i.e. assessments)
set up specifically for them
3. Linked to Segue
-Moodle sites that are created through the Segue will have links in their headers
back to the Segue site they are linked to
-Segue user who can access links to Moodle sites through Segue, will automatically
have their Segue auth used to authenticate them into Moodle and enrol in linked
Moodle sites

/******************************************************************************
 * Variable (ie UI, Security, OS, Maintenance, Mail, User, Permissions,
 * Course Requests, Miscellaneous, Statistics
 ******************************************************************************/
Administration > Configuration > Variables
Chose defaults for all EXCEPT:
Interface -> langmenu: Yes
Interface -> country: United States of America
Interface -> themelist: Segue_Bevelbox,Segue_Shadow,Segue_Tornpaper
Interface -> allowuserthemes: No
Interface -> allowcoursethemes: Yes
Interface -> allowuserblockhiding: Yes
Interface -> showblocksonmodpages: Yes
Mail -> smtphosts: smtp.middlebury.edu
Mail -> smtpuser: ?
Mail -> smtppass: ?
User -> fullnamedisplay: First name + Surname
Permissions -> teacherassignteachers: Yes
Permissions -> restrictmodulesfor: No Courses
Permissions -> restrictbydefault: No
Permissions -> defaultallowedmodules: quiz, resource
Course Requests -> enablecourserequests: No
Miscellaneous -> mymoodleredirect: No



/******************************************************************************
 * Site Settings (ie appearance of front page)
 ******************************************************************************/
Administration > Configuration > Site settings
Full site name: Middlebury Assessments
Short name for site (eg single word): Measure
Front Page Description:
-This is the site for Middlebury online assessments.
Front page format: chose hide for all options
Front page format when logged in: chose hide for all options
Your word for Teacher: Instructor
Your word for Teachers: Instructors
Your word for Student: Student
Your word for Students: Students

/******************************************************************************
 * Themes
 ******************************************************************************/
Administration > Configuration > Site settings
Chose standardlogo

copy to theme directory:
-Segue_Bevelbox
-Segue_Shadow
-Segue_Tornpaper
-Segue_Bevelbox
-standardlogo (modified version)
-standard

remove all themes from theme directory EXCEPT:
-all Segue themes referred to above
-standardlogo and standard

/******************************************************************************
 * UI Language changes
 ******************************************************************************/
Administration > Configuration > Language

To edit, need to switch from en_utf8 to en_utf8_local

en_utf8_local/moodle.php
availablecourses = Measure > Assessments
moodledocslink = Help

en_utf8_local/my.php
mymoodle -> Overview of my courses = Overview of my assessments
mymoodle -> No course information to show. = No assessments to show.

-for other languages, if mods needed then download and install in lang directory
(language packs installed via the GUI can only be edited via the Admin GUI)


/******************************************************************************
 * Modules
 ******************************************************************************/
Administration > Configuration > Modules 
-hide all modules except quiz
-modify module table (UPDATE mdl_module set visible = 0 WHERE name = 'forum')
(this is the only way to turn off the forum module...)

/******************************************************************************
 * Blocks
 ******************************************************************************/
Administration > Configuration > Blocks
Show only the following blocks:
-Administration
-HTML
-Login
-Quiz Results
-Recent Activity
-Remote RSS Feeds

/******************************************************************************
 * Filters
 ******************************************************************************/
Administration > Configuration > Filters
-Hide all filters

/******************************************************************************
 * Backup
 ******************************************************************************/
Administration > Configuration > Backup
Include Modules: Yes with user data
Metacourse: Yes
Users: course
-need to determine schedule, time, and location for backups

/******************************************************************************
 * HTML Editor
 ******************************************************************************/
Administration > Configuration > Editor settings
htmleditor: allow
editorfontfamily: Verdana,Arial,Trebuchet MS,Helvetica,sans-serif
editorfontsize: 12px
editors dropdown menu: 
Verdana: verdana,arial,helvetica,sans-serif
Arial: arial,helvetica,sans-serif
Georgia: georgia,times new roman,times,serif

/******************************************************************************
 * Calendar
 ******************************************************************************/
Administration > Configuration > Calendar
-use defaults

/******************************************************************************
 * Users Authentication Configuration
 ******************************************************************************/
Administration > Users > Authentication

-Moodle will be set up to use email authentication for the creation of new user
accounts and will be configured to authenticate both LDAP users (ldap) and email auth 
users (manual)

1. Set up use an LDAP server as follows:
LDAP server settings:
ldap_host_url: ldap://ad.middlebury.edu/
ldap_version: 3
ldap_preventpassindb: Yes
ldap_bind_dn: juser
ldap_bind_pw: poi987
ldap_user_type: MS Active Directory
ldap_contexts: cn=users,dc=middlebury,dc=edu
ldap_search_sub: Yes
ldap_opt_deref: No
ldap_user_attribute: samaccountname
ldap_memberattribute: memberOf
Force change password: No
Use standard Change Password Page: No
ldap_expiration: No
ldap_expiration_warning: 10
Course creators -> First name: givenName
Course creators -> Surname: sn
Course creators -> Email address: mail
Course creators -> Instructions: Use your Midd username and password to login
Course creators -> Guest login button: Show
Course creators -> Enable user creation: No

2. Set Choose an authentication method = Email-based authentication (default settings)

/******************************************************************************
 * Users Enrolment Plugins
 ******************************************************************************/
Administration > Users > Enrolment Plugins (courses)
chose Internal enrolment and enable


/******************************************************************************
 * Changes to Moodle code
 ******************************************************************************/
A number of changes were made to Moodle code listed below

/******************************************************************************
 * New Account Sign up form changes (for email-based authentication)
 ******************************************************************************/

login/signup_form.html 
-changed to hide username field

login/signup.php
-set $user->username = $user->email;
-changed function validate_form to validate $user->username as $user->email;
-added note about authentication for Midd users

login/forgot_password.php
-added note about authentication for Midd users

moodle/course/lib.php
-changed the print_section_add_menus to comment out following lines:
//$resources["resource&amp;type=$type"] = $name;
//$resources['label'] = get_string('resourcetypelabel', 'resource');


/******************************************************************************
 * Moodle Code Reference
 ******************************************************************************/

lib/datalib.php
-all database functions

lib/moodlelib.php
-all general purpose moodle functions

course/lib.php
-get_all_mods returns all modules
-print_section_add_menus add menus for resources and activities
-modules stored in database table