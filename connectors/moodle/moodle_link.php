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
 * Get the Segue site id from the slot name passed in the request array
 ******************************************************************************/

$site_name = $_REQUEST[site];
$segue_site_id =  db_get_value("slot", "FK_site", "slot_name='".addslashes($site_name)."'");
//$segue_slot_name =  db_get_value("slot", "FK_owner", "slot_name='".addslashes($site_name)."'");
$segue_site_owner =  db_get_value("slot", "FK_owner", "slot_name='".addslashes($site_name)."'");

//$module_id = $_REQUEST[module_id];
//$mod = $_REQUEST[mod];
//ob_end_flush();
//ob_start();

//printpre($_REQUEST);
//printpre($_SESSION);
//printpre("segue_site_id: ".$segue_site_id);
//printpre("segue_user_id: ".$segue_user_id);
//printpre("dbdb_link: ".$dbdb_link);
//printpre("cid: ".$cid);
//printpre("moodle_url: ".$moodle_url);


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

//printpre ($query."<br>");
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
			site_owner_id = '".addslashes($segue_site_owner)."',
		";
	print $query."<br>";
	//exit;
	$r = db_query($query);	
	
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
			FK_seque_user_id = '".addslashes($segue_user_id)."'				
	";

//printpre ($query."<br>");
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
