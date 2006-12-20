<?php // $Id$

require_once("../config.php");
require_once("../lib/datalib.php");
require_once("../lib/moodlelib.php");
require_once("../course/lib.php");
error_reporting(E_ALL & ~E_NOTICE);
/******************************************************************************
 * variables for accessing segue-moodle linking database
 ******************************************************************************/

$dblink_host = "localhost";
$dblink_user = "test";
$dblink_pass = "test";
$dblink_db = "achapin_segue-moodle";

$segue_url = "http://slug.middlebury.edu/~achapin/segue-moodle_connect-v2/";

$cid = mysql_pconnect($dblink_host,$dblink_user,$dblink_pass);
mysql_select_db($dblink_db);

//printpre($_REQUEST);
//printpre($_SESSION);

//print "Moodle-Segue API<hr>";

/******************************************************************************
 * start HTML output
 ******************************************************************************/
?>
<html>
<head>
<title>Segue - Measure Error</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
a {
	color: #003366;
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
.error {
	margin-top: 10px;
	color: #990000;
	font-size: 14px;

}
.back {	
	font-size: 16px;
	align: center;
	padding: 5px;
}

</style>
</head>
<body>
<?

/******************************************************************************
 * Provide links back to referer and/or Measure home
 ******************************************************************************/

if ($_SERVER['HTTP_REFERER'])
	print "<span class='back'><a href='".$_SERVER['HTTP_REFERER']."'>&lt;&lt; back</a></span> | ";

print "<span class='back'><a href='".$segue_url."'>Segue Home</a></span>";

//exit;

/******************************************************************************
 * if id in request from Measure, then build url back to Segue
 ******************************************************************************/ 

if ($_REQUEST['id']) {
	
	/******************************************************************************
	 * Check if the user has a referer listed, if so take them there.
	 ******************************************************************************/ 
	
	$query = "
		SELECT
			referer
		FROM
			authentication
			LEFT JOIN 
				user_link ON auth_id = FK_auth_id
		WHERE
			user_link.system = 'moodle'
			AND user_link.user_id = '".addslashes($_SESSION['USER']->id)."'
			";
	$r = mysql_query($query, $cid);
	
	if (mysql_num_rows($r)) {
	
		$a = mysql_fetch_assoc($r);
		if ($a['referer']) {
			print "Segue site url: ".$a['referer']."<br \>";
			header("Location: ".$a['referer']);		
			exit;
		}
	}
	
	/******************************************************************************
	 * otherwise, go to the corresponding Segue site
	 ******************************************************************************/ 
	$query = "
		SELECT
			site_slot
		FROM
			segue_moodle
		WHERE
			FK_moodle_site_id = '".addslashes($_REQUEST['id'])."'				
	";
	
	//print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	$a = mysql_fetch_assoc($r);
	
	//if no corresponding site, go to Segue home
	if (mysql_num_rows($r) == 0) {
		print "No matching Segue site...<br>";
		//print $segue_url;
		link_log(0, 0, $category="errors",$description="No linked Segue site");
		header("Location: ".$segue_url);
		exit;
	}
	
	//if corresponding site, go to home page of that site
	$segue_slot = $a['site_slot'];
	print "Segue site url: ".$segue_url."sites/".$segue_slot."<br \>";
	header("Location: ".$segue_url."sites/".$segue_slot);
	exit;

}

/******************************************************************************
 * Validate request array values from Segue
 ******************************************************************************/
 
if (!isset($_REQUEST['userid']) || !$_REQUEST['userid']) {
	print "<div class='error'>No user id passed...</div>";	
	print "</body</html>";
	link_log(0, $_REQUEST['siteid'], $category="errors",$description="No Segue user id passed");
	exit;
	
} else if (!isset($_REQUEST['auth_token']) || !$_REQUEST['auth_token']) {
	print "<div class='error'>No authentication token passed...</div>";
	print "</body</html>";
	link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="errors",$description="No authentication token passed");
	exit;
	
} else if (!isset($_REQUEST['siteid']) || !$_REQUEST['siteid']) {
	print "<div class='error'>No site id passed...</div>";
	print "</body</html>";
	link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="errors",$description="No site id passed");
	exit;
	
} else {
	$segue_user_id =  $_REQUEST['userid'];
	$auth_token = $_REQUEST['auth_token'];

	/******************************************************************************
	 * Check that Segue user id and auth_token from request array
	 * match what is in the authentication table in the segue-moodle link database
	 ******************************************************************************/
	$query = "
			SELECT
				auth_token
			FROM
				authentication
			WHERE
				user_id = '".addslashes($_REQUEST['userid'])."'
				AND auth_token = '".addslashes($_REQUEST['auth_token'])."'
				AND DATE_ADD(auth_time, INTERVAL 1 MINUTE) > NOW()
	";
	
	//print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	$a = mysql_fetch_assoc($r);
	
	// failed auth_token test
	if (mysql_num_rows($r) == 0) {
		print "<div class='error'>No matching authentication token or user id or authentication token has expired...</div>";
		print "</body</html>";
		link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="errors",$description="auth_token expired or not passed or no user id");
		exit;
	}

	/******************************************************************************
	 * Check for corresponding Moodle user
	 ******************************************************************************/
	
	$query = "
			SELECT
				user_link.system, user_link.user_id
			FROM
				user_link
			INNER JOIN
				authentication
			ON
				FK_auth_id = auth_id
			WHERE
				authentication.system = 'segue'
				AND
				user_link.system = 'moodle'
				AND
				authentication.user_id = '".addslashes($segue_user_id)."'				
	";
	
	print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	$a = mysql_fetch_assoc($r);
	
	$moodle_user_id = $a['user_id'];
	print "moodle_user_id:".$moodle_user_id."<br \>";
	//exit;
	
	/******************************************************************************
	 * If corresponding Moodle user found in user_link table
	 * make sure that user still exists in Moodle user table
	 ******************************************************************************/
	 
//	 if ($moodle_user_id != 0) {
//	 	$record = get_record('user', 'id', $moodle_user_id);
//	 	if (!$record || $record->deleted == '1') {
//	 		print "corresponding Moodle user no longer exists";
//	 			 		
//			//delete Moodle user from user_link table					
//			$query = "
//				DELETE FROM
//					user_link				
//				WHERE
//					user_id = '".addslashes($moodle_user_id)."'
//			";
//	
//			print $query."<br>";
//			//exit;		
//			$r = mysql_query($query, $cid);
//	 		$moodle_user_id = 0;
//	 	}
//	 }
}

print "moodle_user_id:".$moodle_user_id."<br \>";
print "segue_user_id:".$segue_user_id."<br \>";
//exit;

/******************************************************************************
 * Create new Moodle user if no user exists that corresponds to Segue user
 * add key to that user in user_link table in segue-moodle database
 * New Moodle user code is adapted from:
 * moodle/admin/users.php
 ******************************************************************************/

if ($moodle_user_id == 0) {

	print "<hr>Creating new Moodle user...<br>";
	
	// get info about the linked user
	$query = "
		SELECT 
			username, firstname, lastname,  email, auth_id  
		FROM 
			authentication
		WHERE 
			user_id = '".addslashes($_REQUEST['userid'])."'
			AND auth_token = '".addslashes($_REQUEST['auth_token'])."'
			AND DATE_ADD(auth_time, INTERVAL 1 MINUTE) > NOW()
			
	";
		
	print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	
	// failed auth_token test
	if (mysql_num_rows($r) == 0) {
		print "<div class='error'>No matching authentication token or user id...</div>";
		print "</body</html>";
		exit;
		
	// passed auth_token test so get Segue user info
	} else {
		while ($a = mysql_fetch_assoc($r)) {
			$firstname = ereg_replace("['\"]", "", $a['firstname']);
			$lastname = ereg_replace("['\"]", "", $a['lastname']);
			$email = $a['email'];
			$user_uname = $a['username'];
			$auth_id = $a['auth_id'];
		}
		
		print "firstname: ".$firstname."<br \>";
		print "lastname: ".$lastname."<br \>";
		print "email: ".$email."<br \>";
		print "user_uname: ".$user_uname."<br \>";
		print "auth_id: ".$auth_id."<br \>";
	
	}
	//exit;

	
	//create new moodle user (need user fname, lname, email)
	// adapted from: moodle/admin/users.php

	$user->firstname = $firstname;
	$user->username = $user_uname;
	$user->lastname = $lastname;
	$user->email = $email;
	$user->firstaccess = time();
	$user->auth = "ldap";
	$user->password = "not cached";
	$user->lang = "en_utf8";
	$user->confirmed = 1;
	
//	printpre ($user);a	
//	exit;
	
	if (! ($user->id = insert_record("user", $user)) ) {
		error("Could not add your record to the database!");
		
	} else {
		$moodle_user_id = $user->id;
		
		//update user_link table
				
		$query = "
			UPDATE
				user_link
			SET
				user_id = '".addslashes($moodle_user_id)."',
				system = 'moodle'
			WHERE
			    FK_auth_id = '".addslashes($auth_id)."'
		";

		print $query."<br>";
	//	exit;		
		$r = mysql_query($query, $cid);
		
	}
	print "new moodle user_id: ".$moodle_user_id."<br>";
	link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="user_added",$description="Measure user created");
	//exit;
}

/******************************************************************************
 * end new Moodle user
 ******************************************************************************/
 
/******************************************************************************
* Check for corresponding Moodle site
******************************************************************************/

$query = "
	SELECT
		FK_moodle_site_id
	FROM
		segue_moodle
	WHERE
		FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'				
";

//print $query."<br>";
//exit;
$r = mysql_query($query, $cid);
$a = mysql_fetch_assoc($r);

$moodle_site_id = $a['FK_moodle_site_id'];


/******************************************************************************
 * 	Get the Segue site owner and title from the site_link table
 ******************************************************************************/
print "<hr>Segue site owner and title site";

$query = "
	SELECT 
		site_title, site_slot, site_owner_id, site_theme
	FROM 
		segue_moodle
	WHERE 
		FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'
";
	
//print $query."<br>";
//exit;
$r = mysql_query($query, $cid);

while ($a = mysql_fetch_assoc($r)) {
	$site_title = $a['site_title'];
	$site_slot = $a['site_slot'];
	$site_owner_id = $a['site_owner_id'];
	$site_theme = $a['site_theme'];
}

print "site_title: ".$site_title."<br \>";
print "site_slot: ".$site_slot."<br \>";
print "site_owner_id: ".$site_owner_id."<br \>";
print "site_theme: ".$site_theme."<br \><hr>";

if ($site_theme == "shadowbox") {
	$site_theme = "Segue_Shadow";
} else if ($site_theme == "beveledge") {
	$site_theme = "Segue_Bevelbox";
} else if ($site_theme == "tornpaper") {
	$site_theme = "Segue_Tornpaper";
} else {
	$site_theme = "Segue_Bevelbox";
}

//exit;

/******************************************************************************
 * Make sure corresponding Moodle site still exists in Moodle
 ******************************************************************************/

 if (isset($moodle_site_id) && $moodle_site_id != 0) {
 
	if (!$site = get_record('course', 'id', $moodle_site_id)) {
		print "corresponding Moodle site no longer exists";
		link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="error",$description="linked Measure site deleted");			
		//delete Moodle site from segue_moodle table					
		$query = "
			DELETE FROM
				segue_moodle				
			WHERE
				FK_moodle_site_id = '".addslashes($moodle_site_id)."'
		";

		print $query."<br>";
		//exit;		
		$r = mysql_query($query, $cid);
		$moodle_site_id = 0;
	}
 }
 

print "moodle_site_id: ".$moodle_site_id."<br \>";
//exit;


/******************************************************************************
 * if no corresponding Moodle site, then create one
 * and add key to that site in segue-moodle table
 * new Moodle site code adapted from:
 * moodle/course/edit.php
 * NEED TO TEST ENROLLABLE SETTING
 ******************************************************************************/

if ($moodle_site_id == 0 && $segue_user_id == $site_owner_id) {	

	print "<hr>Creating new Moodle site...<br>";
    require_once("../course/lib.php");
    require_once($CFG->libdir."/blocklib.php");
	
	print "<hr>Creating new Moodle site...<br>";
	
	fix_course_sortorder();
	$form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);	
	$form->format = 'topics';
	$form->timecreated = time();
	$form->sortorder = 100;
	$form->fullname = $site_title;
    $form->shortname = $site_slot;
    $form->summary = "These are assessments for ".$site_title;
    $form->theme = $site_theme;
    $form->visible = 1;
    $form->category = 1;
    $form->enrollable = 0;
	$form->teacher  = "Instructor";
	$form->teachers = "Instructors";
	$form->student  = "Participant";
	$form->students = "Participants";

	if ($newcourseid = insert_record('course', $form)) {  // Set up new course
		$page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);
		blocks_repopulate_page($page); // Return value not checked because you can always edit later
	
		$section = NULL;
		$section->course = $newcourseid;   // Create a default section.
		$section->section = 0;
		$section->id = insert_record("course_sections", $section);
	
		fix_course_sortorder();
		
		add_to_log(SITEID, "course", "new", "view.php?id=$newcourseid", "$form->fullname (ID $newcourseid)");

		fix_course_sortorder();
 
	}
	
	/******************************************************************************
	 * Update the segue_moodle link table
	 ******************************************************************************/
	$moodle_site_id = $newcourseid;
	
	$query = "
		UPDATE
			segue_moodle
		SET
			FK_moodle_site_id = '".addslashes($moodle_site_id)."'
		WHERE
			FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'
			
		";
	print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);

	print "new moodle site_id: ".$moodle_site_id."<br>";
	link_log($_REQUEST['userid'], $_REQUEST['siteid'], $category="site_added",$description="Measure site created");
	//exit;
		
	
}
/******************************************************************************
 * end new Moodle site
 ******************************************************************************/

/******************************************************************************
 * if new course id and the auth user is the site owner then make that user a teacher 
 * moodle_site_id, moodle_user_id and site_owner_id set above
 * segue_user_id from request array
 * code adapted from:
 * moodle/course/teacher.php
 ******************************************************************************/

if ($newcourseid && $segue_user_id == $site_owner_id) {

	print "<hr>New Moodle Course created<br>";	
	print "newcourseid: ".$newcourseid."<br>";
	print "moodle_user_id: ".$moodle_user_id."<br>";

	$newteacher = NULL;
	$newteacher->userid = $moodle_user_id;
	$newteacher->course = $newcourseid;
	$newteacher->authority = 1;   // First teacher is the main teacher
	$newteacher->editall = 1;     // Course creator can edit their own course
	$newteacher->enrol = "manual";     // enroll the teacher in the course
	
	if (!$newteacher->id = insert_record("user_teachers", $newteacher)) {
		error("Could not add you to this new course!");
	}
	print "moodle_user_id: ".$moodle_user_id." made the owner of new moodle course id:".$newcourseid."<br>";
	//exit;
	
	$USER->teacher[$newcourseid] = true;
	$USER->teacheredit[$newcourseid] = true;

/******************************************************************************
 * if the Segue user is not the site owner, then enrol them as student
 * code adapted from:
 * moodle/course/student.php
 ******************************************************************************/
	
} else if ($segue_user_id != $site_owner_id){
	print "<hr>Adding student to site<br>";
	print "moodle_site_id: ".$moodle_site_id;
	$addstudent = $moodle_user_id;
	
	$timestart = $timeend = 0;
	if (! enrol_student($addstudent, $moodle_site_id, $timestart, $timeend)) {
		error("Could not add student with id $addstudent to this course!");
	}
}
//exit;

/******************************************************************************
 * end Moodle permissions assignment (i.e. adding teacher, students)
 ******************************************************************************/

/******************************************************************************
 * Log into Moodle
 * Login code adapted from:
 * moodle/login/index.php
 * Moodle functions get_record (datalib.php) and 
 * get_complete_user_record (lib/moodlelib.php) when passed 'user' or 'username' 
 ******************************************************************************/
 $user_id = $moodle_user_id;
 
if ($user = get_record('user', 'id', $user_id)) {
	print "<hr>logging into Moodle..."; 
	$user = get_complete_user_data('username', $user->username);
	
	$USER = $user;
	add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);
		
	update_user_login_times();
	set_moodle_cookie($USER->username);
	set_login_session_preferences();
	
	//Select password change url
	if (is_internal_auth($USER->auth) || $CFG->{'auth_'.$USER->auth.'_stdchangepassword'}){
		$passwordchangeurl=$CFG->wwwroot.'/login/change_password.php';
	} elseif($CFG->changepassword) {
		$passwordchangeurl=$CFG->changepassword;
	} else {
		$passwordchangeurl = '';
	}
	
  // check whether the user should be changing password
	if (get_user_preferences('auth_forcepasswordchange', false) || $frm->password == 'changeme'){
		if ($passwordchangeurl != '') {
			redirect($passwordchangeurl);
		} else {
			error("You cannot proceed without changing your password. 
				   However there is no available page for changing it.
				   Please contact your Moodle Administrator.");
		}
	}
			
 /// Prepare redirection
	if (user_not_fully_set_up($USER)) {
		$urltogo = $CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.SITEID;
		// We don't delete $SESSION->wantsurl yet, so we get there later

	} else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
		$urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
		unset($SESSION->wantsurl);

	} else {
		// no wantsurl stored or external - go to homepage
		$urltogo = $CFG->wwwroot.'/';
		unset($SESSION->wantsurl);
	}
	
	  /// Go to my-moodle page instead of homepage if mymoodleredirect enabled
	if (!isadmin() and !empty($CFG->mymoodleredirect) and !isguest()) {
		if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
			$urltogo = $CFG->wwwroot.'/my/';
		}
	}

	 reset_login_count();
	 
	//$urltogo = $CFG->wwwroot."/mod/quiz/view.php?id=$module_id";
	$urltogo = $CFG->wwwroot.'/course/view.php?id='.$moodle_site_id;
	//$urltogo = $CFG->wwwroot;
	
	
	if ($_REQUEST[mod]) {
		$module_url = $CFG->wwwroot."/mod/".$_REQUEST[mod]."/view.php?id=".$module_id;
		print $module_url;
		header("Location: ".$module_url);
	} else {
		//print "<hr>Go to: <a href='".$CFG->wwwroot."/mod/".$_REQUEST[mod]."/view.php?id=$module_id'>module id= ".$module_id."</a> | ";
		print "<a href='".$urltogo."'>Moodle Home</a><hr>";
	}
	redirect($urltogo);
}

function printpre($array, $return=FALSE) {
	$string = "\n<pre>";
	$string .= print_r($array, TRUE);
	$string .= "\n</pre>";
	
	if ($return)
		return $string;
	else
		print $string;
}

function link_log($auth_id="",$site_link_id="",$category="event",$description="") {
	global $dblink_host,$dblink_user,$dblink_pass;
	$cid = mysql_pconnect($dblink_host,$dblink_user,$dblink_pass);
	
	$query = " 
		INSERT INTO 
			logs
		SET
			FK_auth_id = '".addslashes($auth_id)."',
			FK_site_link = '".addslashes($site_link_id)."',
			category = '".addslashes($category)."',
			description = '".addslashes($description)."'
	";
	//print $query;
	//exit;
	$r = mysql_query($query, $cid);
}


?>
