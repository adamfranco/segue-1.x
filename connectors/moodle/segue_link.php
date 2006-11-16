<?php // $Id$
session_start();// start the session manager :) -- important, as we just learned

require_once("config.php");
require_once("lib/datalib.php");
require_once("lib/moodlelib.php");
require_once("course/lib.php");
//moodle_link.php?site=test157
//require_once("functions.inc.php");
//require_once("config.inc.php");
//require_once("dbwrapper.inc.php");


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
 * Check for corresponding Moodle user
 ******************************************************************************/

$query = "
		SELECT
			FK_moodle_user_id
		FROM
			user_link
		WHERE
			FK_seque_user_id = '".addslashes($_REQUEST['userid'])."'				
	";
	
print $query."<br>";
$r = mysql_query($query, $cid);
$a = mysql_fetch_assoc($r);


/******************************************************************************
 * If no corresponding Moodule user, then create that user and
 * add key to that user in user_link table in segue-moodle database
 ******************************************************************************/

if ($a['FK_moodle_user_id'] == 0) {
	print "<hr>Creating Moodle user...<br>";

	$query = "
		SELECT 
			username, firstname, lastname,  email  
		FROM 
			authentication
		WHERE userid = '".addslashes($_REQUEST['userid'])."'
	";
		
	print $query."<br>";
	$r = mysql_query($query, $cid);

	while ($a = mysql_fetch_assoc($r)) {
		$firstname = $a['firstname'];
		$lastname = $a['lastname'];
		$email = $a['email'];
		$user_uname = $a['username'];
	}
	
	print "firstname: ".$firstname;
	print "lastname: ".$lastname;
	print "email: ".$email;
	print "user_uname: ".$user_uname;
	//exit;

	
	//create new moodle user (need user fname, lname, email)
	//other fields: username?, password?
	$user->firstname = $firstname;
	$user->username = $user_uname;
	$user->lastname = $lastname;
	$user->email = $email;
	$user->firstaccess = time();
	$user->auth = "manual";
	$user->confirmed = 1;
	
//  printpre2($user);
	
	if (! ($user->id = insert_record("user", $user)) ) {
		error("Could not add your record to the database!");
	} else {
		$moodle_user_id = $user->id;
		//update Segue moodle table
		$query = "
			INSERT INTO
				user_link
			SET
				FK_moodle_user_id = '".addslashes($moodle_user_id)."'
			WHERE
				WHERE FK_seque_user_id = '".addslashes($_REQUEST['userid'])."'
		";
		print $query."<br>";
		$r = mysql_query($query, $cid);
		
	}
	print "new moodle user_id: ".$moodle_user_id."<br>";
}

exit;

/******************************************************************************
 * user_id will eventually use the Segue user_id
 ******************************************************************************/
 $segue_user =  $_REQUEST['userid'];
 $user_id = $moodle_user_id;
 
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
print $query."<br>";
$r = mysql_query($query, $cid);
$a = mysql_fetch_assoc($r);


/******************************************************************************
 * if no corresponding Moodle site, then create one
 * and add key to that site in segue moodle table
 ******************************************************************************/
 
if ($a['FK_moodle_site_id'] == 0) {	
	print "<hr>Creating Moodle site...<br>";
    require_once("course/lib.php");
    require_once("$CFG->libdir/blocklib.php");

	//need to add site owner and title to site_link table
	$query = "
		SELECT 
			sitetitle, siteowner 
		FROM 
			site_link
		WHERE userid = '".addslashes($_REQUEST['siteid'])."'
	";
		
	print $query."<br>";
	$r = mysql_query($query, $cid);

	while ($a = mysql_fetch_assoc($r)) {
		$sitetitle = $a['sitetitle'];
		$siteowner = $a['siteowner'];
	}
	exit;
	
	fix_course_sortorder();
	$form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);	
	$form->format = 'social';
	$form->timecreated = time();
	$form->sortorder = 100;
	$form->fullname = $fullname;
    $form->shortname = $fullname;
    $form->summary = $fullname;
    $form->visible = 1;
    $form->category = 1;
	$form->teacher  = "Owner";
	$form->teachers = "Owners";
	$form->student  = "Participant";
	$form->students = "Participants";

	//printpre2($form);
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
	
	//update Segue moodle table
	$moodle_site_id = $newcourseid;
	
	//update Segue moodle table
	$query = "
		INSERT INTO
			site_link
		SET
			FK_moodle_site_id = '".addslashes($moodle_site_id)."'
		WHERE
			FK_segue_site_id = '".addslashes($_REQUEST['siteid'])."'
			
		";
	print $query."<br>";
	$r = mysql_query($query, $cid);

	print "new moodle site_id: ".$moodle_site_id."<br>";

}


 
/******************************************************************************
 * if new course id and the auth user is the site owner then make user a teacher 
 ******************************************************************************/

if ($newcourseid && $moodle_user_id && $segue_user == $site_owner) {
	print "<hr>New Moodle Course created<br>";	
	print "newcourseid: ".$newcourseid."<br>";
	print "moodle_user_id: ".$moodle_user_id."<br>";
	print "(segue_user: ".$segue_user.")<br>";
	print "(site_owner: ".$site_owner.")<br>";
	$newteacher = NULL;
	$newteacher->userid = $moodle_user_id;
	$newteacher->course = $newcourseid;
	$newteacher->authority = 1;   // First teacher is the main teacher
	$newteacher->editall = 1;     // Course creator can edit their own course
	
	if (!$newteacher->id = insert_record("user_teachers", $newteacher)) {
		error("Could not add you to this new course!");
	}
	print "moodle_user_id: ".$moodle_user_id." made the owner of new moodle course id:".$newcourseid."<br>";
	
	$USER->teacher[$newcourseid] = true;
	$USER->teacheredit[$newcourseid] = true;
}

/******************************************************************************
 * Log into Moodle
 * Moodle functions get_record (datalib.php) and 
 * get_complete_user_record (lib/moodlelib.php) when passed 'user' or 'username' 
 ******************************************************************************/
 
if ($user = get_record('user', 'id', $user_id)) {
	$user = get_complete_user_data('username', $user->username);
	
	$USER = $user;
	add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);
				
	update_login_count();
	update_user_login_times();
	set_moodle_cookie($user->username);
	set_login_session_preferences();
	reset_login_count();
	
	$cookie = get_moodle_cookie();
		
	//Select password change url
	if (is_internal_auth() || $CFG->{'auth_'.$USER->auth.'_stdchangepassword'}){
		$passwordchangeurl=$CFG->wwwroot.'/login/change_password.php';
	} elseif($CFG->changepassword) {
		$passwordchangeurl=$CFG->changepassword;
	} 
	
	// check whether the user should be changing password
	if (get_user_preferences('auth_forcepasswordchange', false) || $frm->password == 'changeme'){
		if (isset($passwordchangeurl)) {
			redirect($passwordchangeurl);
		} else {
			error("You cannot proceed without changing your password. 
				   However there is no available page for changing it.
				   Please contact your Moodle Administrator.");
		}
	}
			
	if (user_not_fully_set_up($USER)) {
		$urltogo = $CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.SITEID;
		// We don't delete $SESSION->wantsurl yet, so we get there later
	
	} else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
		$urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
		unset($SESSION->wantsurl);
	
	} else {
		$urltogo = $CFG->wwwroot.'/';      /// Go to the standard home page
		unset($SESSION->wantsurl);         /// Just in case
	}
	
	$urltogo = $CFG->wwwroot."/mod/quiz/view.php?id=$module_id";
	
	//require_once("config.inc.php");
	//require_once("dbwrapper.inc.php");

	//get info from Segue tables
	//print $id."<br>";
	//print $user_id."<br>";
	
	if ($_REQUEST[mod]) {
		$module_url = $CFG->wwwroot."/mod/".$_REQUEST[mod]."/view.php?id=".$module_id;
		print $module_url;
		header("Location: ".$module_url);
	} else {
		print "<hr>Go to: <a href='".$CFG->wwwroot."/mod/".$_REQUEST[mod]."/view.php?id=$module_id'>module id= ".$module_id."</a> | ";
		print "<a href='".$CFG->wwwroot."/'>Moodle Home</a><hr>";
	}
	//redirect($urltogo);
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
