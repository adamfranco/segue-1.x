<?php // $Id$

require_once("config.php");
require_once("lib/datalib.php");
require_once("lib/moodlelib.php");
require_once("course/lib.php");
//moodle_link.php?site=test157
//require_once("functions.inc.php");
//require_once("config.inc.php");
//require_once("dbwrapper.inc.php");

/******************************************************************************
 * variables for accessing segue-moodle linking database
 ******************************************************************************/

$dblink_host = "localhost";
$dblink_user = "test";
$dblink_pass = "test";
$dblink_db = "achapin_segue-moodle";

$cid = mysql_pconnect($dblink_host,$dblink_user,$dblink_pass);
mysql_select_db($dblink_db);

printpre($_REQUEST);
//printpre($_SESSION);

print "Moodle-Segue API<hr>";

/******************************************************************************
 * Validate request array values
 ******************************************************************************/
 
if (!isset($_REQUEST['userid'])) {
	print "no user id passed<br>";
	exit;
	
} else if (!isset($_REQUEST['auth_token'])) {
	print "no auth_token passed<br>";
	exit;
	
} else if (!isset($_REQUEST['siteid'])) {
	print "no site id passed<br>";
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
				userid = '".addslashes($_REQUEST['userid'])."'
			AND
				auth_token = '".addslashes($_REQUEST['auth_token'])."'
	";
	
	//print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	$a = mysql_fetch_assoc($r);
	
	// failed auth_token test
	if (mysql_num_rows($r) == 0) {
		print "no matching auth_token or userid...<br>";
		exit;
	}

	/******************************************************************************
	 * Check for corresponding Moodle user
	 ******************************************************************************/
	
	$query = "
			SELECT
				FK_moodle_user_id
			FROM
				user_link
			WHERE
				FK_segue_user_id = '".addslashes($_REQUEST['userid'])."'				
	";
	
	//print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	$a = mysql_fetch_assoc($r);
	
	$moodle_user_id = $a['FK_moodle_user_id'];
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
	
	$query = "
		SELECT 
			username, firstname, lastname,  email  
		FROM 
			authentication
		WHERE 
			userid = '".addslashes($_REQUEST['userid'])."'
		AND
			auth_token = '".addslashes($_REQUEST['auth_token'])."'
	";
		
	print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);
	
	// failed auth_token test
	if (mysql_num_rows($r) == 0) {
		print "no matching auth_token or userid...<br>";
		exit;
		
	// passed auth_token test so get Segue user info
	} else {
		while ($a = mysql_fetch_assoc($r)) {
			$firstname = $a['firstname'];
			$lastname = $a['lastname'];
			$email = $a['email'];
			$user_uname = $a['username'];
		}
		
		print "firstname: ".$firstname."<br \>";
		print "lastname: ".$lastname."<br \>";
		print "email: ".$email."<br \>";
		print "user_uname: ".$user_uname."<br \>";
	
	}
	//exit;

	
	//create new moodle user (need user fname, lname, email)
	// see: moodle/admin/users.php

	$user->firstname = $firstname;
	$user->username = $user_uname;
	$user->lastname = $lastname;
	$user->email = $email;
	$user->firstaccess = time();
	$user->auth = "ldap";
	$user->password = "not cached";
	$user->lang = "en_utf8";
	$user->confirmed = 1;
	
	
	if (! ($user->id = insert_record("user", $user)) ) {
		error("Could not add your record to the database!");
		
	} else {
		$moodle_user_id = $user->id;
		
		//update Segue moodle table
		$query = "
			UPDATE
				user_link
			SET
				FK_moodle_user_id = '".addslashes($moodle_user_id)."'
			WHERE
			    FK_segue_user_id = '".addslashes($_REQUEST['userid'])."'
		";
		print $query."<br>";
		$r = mysql_query($query, $cid);
		
	}
	print "new moodle user_id: ".$moodle_user_id."<br>";
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
		site_link
	WHERE
		FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'				
";

//print $query."<br>";
//exit;
$r = mysql_query($query, $cid);
$a = mysql_fetch_assoc($r);

$moodle_site_id = $a['FK_moodle_site_id'];
print "moodle_site_id: ".$moodle_site_id."<br \>";
//exit;

/******************************************************************************
 * 	Get the Segue site owner and title from the site_link table
 ******************************************************************************/
print "<hr>Segue site owner and title site";

$query = "
	SELECT 
		site_title, site_slot, site_owner_id, site_theme
	FROM 
		site_link
	WHERE 
		FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'
";
	
print $query."<br>";
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
//exit;

/******************************************************************************
 * if no corresponding Moodle site, then create one
 * and add key to that site in segue moodle table
 * new Moodle site code adapted from:
 * moodle/course/edit.php
 ******************************************************************************/

if ($moodle_site_id == 0 && $segue_user_id == $site_owner_id) {	

	print "<hr>Creating new Moodle site...<br>";
    require_once("course/lib.php");
    require_once("$CFG->libdir/blocklib.php");
	
	fix_course_sortorder();
	$form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);	
	$form->format = 'topics';
	$form->timecreated = time();
	$form->sortorder = 100;
	$form->fullname = $site_title;
    $form->shortname = $site_slot;
    $form->summary = "";
  //  $form->theme = "Segue_Bevelbox";
    $form->visible = 1;
    $form->category = 1;
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
	 * Update the segue-moodle link table
	 ******************************************************************************/
	$moodle_site_id = $newcourseid;
	
	$query = "
		UPDATE
			site_link
		SET
			FK_moodle_site_id = '".addslashes($moodle_site_id)."'
		WHERE
			FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'
			
		";
	print $query."<br>";
	//exit;
	$r = mysql_query($query, $cid);

	print "new moodle site_id: ".$moodle_site_id."<br>";
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
	print "moodle_site_id".$moodle_site_id;
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


/******************************************************************************
 * if no module or moodle id passed then adding new module
 * called from add_story_form_1_item_inc
 ******************************************************************************/

//if (!$module_id || !moodle_id) {
//	$site_id = $_REQUEST[site_id];
//	$course_id = $moodle_site_id; 
//	$section = 1;
//	$shortname = $_REQUEST[shortname];
//	$fullname = db_get_value("site", "site_title", "site_id = $site_id");	
//	print "To create an instance of a Moodle module requires: course_id and section<br>";
//	print "If the course_id passed is not in Moodle course table, then new Moodle course needs to be created<br><br>";
//	print "course_id = ".$course_id."<br>";
//	print "section = ".$section."<br>";
//	print "shortname = ".$shortname."<br>";
//	print "fullname = ".$fullname."<br>";
//	
////	print "<br><hr>moodle_id= ".$moodle_id."<br>";
////	print "<br>moodle_type= ".$moodle_type."<br>";
//	
//	//$course_id=2;
//	//$section = 1;
//	
//	$addquiz = "<a href='$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=quiz'>";
//	$addquiz .= "add quiz</a>";
//	
//	$addwiki = "<a href='$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=wiki'>";
//	$addwiki .= "add wiki</a>";
//
//	$addchat = "<a href='$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=chat'>";
//	$addchat .= "add chat</a>";
//	
//	print "add: ".$addquiz." | ".$addwiki." | ".$addchat."<br><br>";
//
//	if (!isset($straddactivity)) {
//		$straddactivity = get_string('addactivity');
//		$straddresource = get_string('addresource');
//	}
//	
//	get_all_mods($course_id, $mods, $modnames, $modnamesplural, $modnamesused);
//	$activities = popup_form2("$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
//					$modnames, "section$section", "", $straddactivity, 'mods', $straddactivity, true);
//	
//	print $activities;
//	
//}



/******************************************************************************
 * if module_id passed w/o user_id then module was created or updated and module data
 * needs to be passed to Segue
 ******************************************************************************/

//if (isset($_REQUEST[module_id]) && !$user_id) {
//	print "<hr>Created/updating Moodle module...<br>";
//
//	$module_id = $_REQUEST[module_id];
//	
//	if (!$mod) {
//		$module_type = $_REQUEST[module_type];
//	} else {
//		$module_type = $mod;
//	}
//	
//	if (! $moduleinstance_id = get_field("course_modules", "instance", "id", $module_id)) {
//	   error("There is no moodle_instance with id $moodleinstance_id");
//	}
//	
//	if (! $module_name = get_field($module_type, "name", "id", $moduleinstance_id)) {
//		error("The $mod_name with id1 $moodleinstance_id is missing");
//	}
//
////	if (! $module_intro = get_field($module_type, "intro", "id", $moduleinstance_id)) {
////		error("The $mod_intro with id2 $moodleinstance_id is missing");
////	}
//		
//	
//	print "module_id= ".$module_id."<br>";
//	print "module_type= ".$module_type."<br>";
//	print "module_name= ".$module_name."<br>";
//	print "module_intro= ".$module_intro."<br>";
//	?>
//	
//	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//	<html> 
//	<head>
//	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//	<title>Segue-Moodle API</title> 
//	
//	<script lang="JavaScript"> 
//	
//	function useFile(moduleID,moduleType,moduleName) { 
//		o = opener.document.addform; 
//		o.moodleid.value=moduleID; 
//		o.moodletype.value=moduleType; 
//		o.title.value=moduleName;
//		o.submit(); 
//		window.close(); 
//	} 
//	</script>
//	<?
//	print "<input type=button name='use' value='use' onClick=\"useFile('".$module_id."','".$module_type."','".$module_name."')\">\n";
//	print "</form>";
//
//	exit();
//}




function popup_form2($common, $options, $formname, $selected='', $nothing='choose', $help='', $helptext='', $return=false, $targetwindow='self') {

//    global $CFG;
//    static $go, $choose;   /// Locally cached, in case there's lots on a page
//
//    if (empty($options)) {
//        return '';
//    }
//
//    if (!isset($go)) {
//        $go = get_string('go');
//    }
//
//    if ($nothing == 'choose') {
//        if (!isset($choose)) {
//            $choose = get_string('choose');
//        }
//        $nothing = $choose.'...';
//    }
//
//    $startoutput = '<form action="'.$CFG->wwwroot.'/course/jumpto.php"'.
//                        ' method="get"'.
//                        ' target="self"'.
//                        ' name="'.$formname.'"'.
//                        ' class="popupform">';
//
//    $output = '<select name="jump" onchange="'.$targetwindow.'.location=document.'.$formname.
//                       '.jump.options[document.'.$formname.'.jump.selectedIndex].value;">'."\n";
//
//    if ($nothing != '') {
//        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
//    }
//
//    $inoptgroup = false;
//    foreach ($options as $value => $label) {
//
//        if (substr($label,0,2) == '--') { /// we are starting a new optgroup
//
//            /// Check to see if we already have a valid open optgroup
//            /// XHTML demands that there be at least 1 option within an optgroup
//            if ($inoptgroup and (count($optgr) > 1) ) {
//                $output .= implode('', $optgr);
//                $output .= '   </optgroup>';
//            }
//
//            unset($optgr);
//            $optgr = array();
//
//            $optgr[]  = '   <optgroup label="'. substr($label,2) .'">';   // Plain labels
//
//            $inoptgroup = true; /// everything following will be in an optgroup
//            continue;
//
//        } else {
//            $optstr = '   <option value="' . $common . $value . '"';
//
//            if ($value == $selected) {
//                $optstr .= ' selected="selected"';
//            }
//
//            if ($label) {
//                $optstr .= '>'. $label .'</option>' . "\n";
//            } else {
//                $optstr .= '>'. $value .'</option>' . "\n";
//            }
//
//            if ($inoptgroup) {
//                $optgr[] = $optstr;
//            } else {
//                $output .= $optstr;
//            }
//        }
//
//    }
//
//    /// catch the final group if not closed
//    if ($inoptgroup and count($optgr) > 1) {
//        $output .= implode('', $optgr);
//        $output .= '    </optgroup>';
//    }
//
//    $output .= '</select>';
//    $output .= '<noscript id="noscript'.$formname.'" style="display: inline;">';
//    $output .= '<input type="submit" value="'.$go.'" /></noscript>';
//    $output .= '<script type="text/javascript">'.
//               "\n<!--\n".
//               'document.getElementById("noscript'.$formname.'").style.display = "none";'.
//               "\n-->\n".'</script>';
//    $output .= '</form>' . "\n";
//
//    if ($help) {
//        $button = helpbutton($help, $helptext, 'moodle', true, false, '', true);
//    } else {
//        $button = '';
//    }
//
//    if ($return) {
//        return $startoutput.$button.$output;
//    } else {
//        echo $startoutput.$button.$output;
//    }
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



?>
