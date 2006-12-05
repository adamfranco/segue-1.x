<?php // $Id$
session_start();// start the session manager :) -- important, as we just learned

//require_once("moodle/config.php");
//require_once("moodle/lib/datalib.php");
//require_once("moodle/lib/moodlelib.php");
//require_once("moodle/course/lib.php");
//moodle_link.php?site=test157
require_once("../../functions.inc.php");
require_once("../../config.inc.php");
require_once("../../dbwrapper.inc.php");


$dbdb_link = "achapin_segue-moodle";
$moodle_url = "http://slug.middlebury.edu/~achapin/moodle163";

$cid = db_connect ($dbhost, $dbuser, $dbpass, $dbdb);

$segue_user_id = $_SESSION[aid];
$username = $_SESSION[auser];
$useremail = $_SESSION[aemail];
$userfname = $_SESSION[afname];

$names = split(" ",$userfname);
$firstname = trim($names[1]);
$lastname = trim($names[2]);

/******************************************************************************
 * Make sure there is an authenticated Segue user
 ******************************************************************************/
if (!isset($segue_user_id)) {
	print "You must be logged into Segue to use this link";
	exit;
}


/******************************************************************************
 * Get the Segue site id from the slot name passed in the request array
 ******************************************************************************/

$site_name = $_REQUEST[site];
$segue_site_id =  db_get_value("slot", "FK_site", "slot_name='".addslashes($site_name)."'");
$node_id = $_REQUEST[node];
//$page_id = db_get_value("story", "FK_page", "story_id='".addslashes($node_id)."'");
//$section_id = db_get_value("story", "FK_section", "page_id='".addslashes($page_id)."'");

//$thisPage =& new page($_REQUEST[site],$section_id,$page_id,$node_id, $thisPage);
//$thisNode =& new story($_REQUEST[site],$section_id,$page_id,$node_id, $thisPage);

/******************************************************************************
 * get information about the Segue user
 * if the Segue user is an editor of the Segue site then they 
 * become a teacher in the linked Moodle site
 * if the Segue user has view permission only, then they become students
 * in the in the linked Moodle site
 ******************************************************************************/

$segue_site_owner =  db_get_value("slot", "FK_owner", "slot_name='".addslashes($site_name)."'");

//$query = "
//	SELECT
//		FK_editor
//	FROM
//		permission
//	WHERE
//		FK_editor = '$segue_user_id'
//	AND
//		FK_scope_id = '$node_id'
//	AND
//		permission_scope_type = 'story'
//	AND
//		permission_value = 'e'
//	";
//
//$r = db_query($query);
//
//if (db_num_rows($r) != 0) {
//	print "user $segue_user_id is an editor of node $node_id<br>";	
//	$segue_site_editor = $segue_user_id;
//} else {
//	print "user $segue_user_id is NOT an editor of node $node_id<br>";
//}
//exit;

print "Moodle-Segue API<hr>";

$cid2 = db_connect ($dbhost, $dbuser, $dbpass, $dbdb_link);
//printpre("cid2: ".$cid2);


/******************************************************************************
 * Check for corresponding Moodle site
 ******************************************************************************/
$query = "
		SELECT
			FK_moodle_site_id
		FROM
			site_link
		WHERE
			FK_segue_site_id = '".addslashes($segue_site_id)."'				
	";

printpre ($query."<br>");
$r = db_query($query);

if (db_num_rows($r) != 0) {
	print "linked moodle site found<br>";
	
} else {
	print "no linked moodle site found<br>";
	$query = "
		INSERT INTO
			site_link
		SET
			FK_segue_site_id = '".addslashes($segue_site_id)."',
			site_title = '".addslashes($site_name)."',
			site_owner_id = '".addslashes($segue_site_owner)."'
		";
	print $query."<br>";
	
	$r = db_query($query);	
	//exit;
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
			FK_segue_user_id = '".addslashes($segue_user_id)."'				
	";

printpre ($query."<br>");

$r = db_query($query);

if (db_num_rows($r) != 0) {
	print "linked moodle user found<br>";
	
} else {
	print "no linked moodle user found<br>";
	$query = "
		INSERT INTO
			authentication
		SET
			username = '".addslashes($_SESSION[auser])."',
			firstname = '".addslashes($firstname)."',
			lastname = '".addslashes($lastname)."',
			email = '".addslashes($_SESSION[aemail])."',
			userid = '".addslashes($_SESSION[aid])."'
		";
	print $query."<br>";
	$r = db_query($query);	
	$query = "
		INSERT INTO
			user_link
		SET
			FK_segue_user_id = '".addslashes($segue_user_id)."'
		";
	print $query."<br>";
	$r = db_query($query);		
		
}


//exit;
header("Location: ".$moodle_url."/segue_link.php?userid=".addslashes($segue_user_id)."&siteid=".addslashes($segue_site_id));
//exit;



?>
